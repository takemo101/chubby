<?php

namespace Takemo101\Chubby\Log;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Processor\UidProcessor;
use Symfony\Component\Uid\Uuid;

/**
 * Logger factory that outputs to a file.
 */
final class FileLoggerFactory implements LoggerFactory
{
    /**
     * constructor
     *
     * @param string $path
     * @param string $filename
     * @param Level $level
     */
    public function __construct(
        private readonly string $path,
        private readonly string $filename,
        private readonly Level $level,
    ) {
        //
    }

    /**
     * Create logger.
     *
     * @param string|null $name
     * @return LoggerInterface
     */
    public function create(?string $name = null): LoggerInterface
    {
        $logger = new Logger($name ?? Uuid::v4()->toRfc4122());

        $logger->pushProcessor(new UidProcessor());

        $logger->pushHandler($this->createHandler());

        return $logger;
    }

    /**
     * Create file handler.
     *
     * @return HandlerInterface
     */
    private function createHandler(): HandlerInterface
    {
        $handler = new RotatingFileHandler(
            filename: $this->createPath(),
            maxFiles: 0,
            level: $this->level,
            bubble: true,
            filePermission: 0777,
        );

        $handler->setFormatter(
            new LineFormatter(
                format: null,
                dateFormat: null,
                allowInlineLineBreaks: true,
                ignoreEmptyContextAndExtra: true,
                includeStacktraces: true,
            ),
        );

        return $handler;
    }

    /**
     * Create log file path.
     *
     * @return string
     */
    protected function createPath(): string
    {
        return sprintf(
            '%s/%s',
            $this->path,
            $this->filename,
        );
    }
}
