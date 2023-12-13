<?php

namespace Takemo101\Chubby\Console\Command;

use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class ServeProcessOutputHandler
{
    /**
     * @var string
     */
    public const RequestHostRegex = '/\]\s(.+):(\d+)\s(?:(?:\w+$)|(?:\[.*))/';

    /**
     * constructor
     *
     * @param OutputInterface $output
     * @param boolean $isEnabledCliServerWorkers
     * @param DateTimeInterface $startedAt
     * @param ServeProcessRequestPool $requestPool
     */
    public function __construct(
        private OutputInterface $output,
        private bool $isEnabledCliServerWorkers = false,
        private DateTimeInterface $startedAt = new DateTimeImmutable(),
        private ServeProcessRequestPool $requestPool = new ServeProcessRequestPool(),
    ) {
        //
    }

    /**
     * Handle the given PHP server output.
     *
     * @param string $type
     * @param string $buffer
     * @return void
     */
    public function __invoke(
        string $type,
        string $buffer,
    ): void {
        $lines = explode("\n", $buffer);

        $lines = array_filter($lines, fn ($line) => $line !== '');

        foreach ($lines as $line) {
            if (str_contains($line, 'Development Server (http')) {

                $this->output->writeln($line);
                $this->output->writeln('<fg=yellow;options=bold>Press Ctrl+C to stop the server</>');

                $this->output->writeln('<fg=gray>Started at:</> ' . $this->startedAt->format('Y-m-d H:i:s'));
            }
            // When the request is started, add the request information
            elseif (str_contains($line, ' Accepted')) {

                $startedAt = $this->getDateTimeFromLine($line);
                $host = $this->getRequestHostFromLine($line);

                $this->requestPool->push(
                    host: $host,
                    startedAt: $startedAt,
                );
            }
            // When the request is completed, output the request message and delete the request information.
            elseif (str_contains($line, ' Closing')) {

                $host = $this->getRequestHostFromLine($line);

                $request = $this->requestPool->pop($host);

                if (!$request) {
                    return;
                }

                $this->output->write($host);

                if ($message = $request->message) {
                    $this->output->write(" <fg=green>{$message}</>");
                }

                $startedAt = $request->startedAt;

                $closedAt = $this->getDateTimeFromLine($line);

                $interval = $startedAt->diff($closedAt);

                $this->output->writeln(" {$startedAt->format('Y-m-d H:i:s')} ~ <fg=gray>{$interval->s}s</>");
            }
            // If you have a request host information, treat it as a request message
            elseif ($this->hasRequestHostFromLine($line)) {

                $host = $this->getRequestHostFromLine($line);

                if ($request = $this->requestPool->pop($host)) {
                    // Acquire a character string excluding the date and time and host information and set it as a message
                    $excluded = $this->getExcludeDateTimeFromLine($line);
                    $message = trim(str_replace($host, '', $excluded));

                    $this->requestPool->push(
                        host: $host,
                        startedAt: $request->startedAt,
                        message: $message,
                    );
                }
                // If there is no request information, output it as a warning message
                else {
                    $this->output->writeln("<fg=red;options=bold>{$line}</>");
                }
            }
            // Thinking as a warning message
            else {

                $this->output->writeln("<fg=red;options=bold>{$line}</>");
            }
        }
    }

    private function getDateTimeRegex(): string
    {
        return $this->isEnabledCliServerWorkers > 1
            ? '/^\[\d+]\s\[([a-zA-Z0-9: ]+)\]/'
            : '/^\[([^\]]+)\]/';
    }

    /**
     * Get a character string excluding the date and time information
     *
     * @param string $line
     * @return string
     * @throws RuntimeException
     */
    private function getExcludeDateTimeFromLine(string $line): string
    {
        $excluded = preg_replace($this->getDateTimeRegex(), '', $line);

        $result = $excluded
            ? $excluded
            : $line;

        return trim($result);
    }

    /**
     * Get the date from the given PHP server output.
     *
     * @param string $line
     * @return DateTimeInterface
     * @throws RuntimeException
     */
    private function getDateTimeFromLine(string $line): DateTimeInterface
    {
        $regex = $this->getDateTimeRegex();

        /** @var string */
        $line = str_replace('  ', ' ', $line);

        if (
            preg_match($regex, $line, $matches) === false
            || !isset($matches[1])
        ) {
            throw new RuntimeException('Unable to parse date from server output.');
        }

        $datetime = DateTimeImmutable::createFromFormat('D M d H:i:s Y', $matches[1]);

        // If the date and time cannot be acquired, the current date and time is set
        return $datetime === false
            ? new DateTimeImmutable()
            : $datetime;
    }

    /**
     * Get the request port from the given PHP server output.
     *
     * @param string $line
     * @return string
     */
    private function getRequestHostFromLine(string $line): string
    {
        if (
            preg_match(self::RequestHostRegex, $line, $matches) === false
            || !isset($matches[1], $matches[2])
        ) {
            throw new RuntimeException('Unable to parse port from server output.');
        }

        return $matches[1] . ':' . $matches[2];
    }

    /**
     * Determine if the given PHP server output has a request host.
     *
     * @param string $line
     * @return boolean
     */
    private function hasRequestHostFromLine(string $line): bool
    {
        return preg_match(self::RequestHostRegex, $line) === 1;
    }
}
