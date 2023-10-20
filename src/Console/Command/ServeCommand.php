<?php

namespace Takemo101\Chubby\Console\Command;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Takemo101\Chubby\Support\Environment;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Takemo101\Chubby\Support\ApplicationPath;

/**
 * Use PHP's built-in web server.
 */
final class ServeCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        /** @var Environment */
        $env = $this->getContainer()->get(Environment::class);

        $port = $env->get('SERVER_PORT', '8080');
        $host = $env->get('SERVER_HOST', 'localhost');
        $script = $env->get('SERVER_SCRIPT', '/index.php');

        $this
            ->setName('serve')
            ->setDescription("Use PHP's built-in web server")
            ->addOption(
                name: 'port',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Port number',
                default: $port,
            )
            ->addOption(
                name: 'host',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Host name',
                default: $host,
            )
            ->addOption(
                name: 'script',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'PHP script file in document root',
                default: $script,
            );
    }

    /**
     * Execute command process.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    public function handle(
        InputInterface $input,
        OutputInterface $output,
        ApplicationPath $path,
    ) {
        /** @var string */
        $port = $input->getOption('port');
        /** @var string */
        $host = $input->getOption('host');
        /** @var string */
        $script = $input->getOption('script');

        $environments = [
            'APP_BASE_PATH' => $path->getBasePath(),
            ...collect($_ENV)->mapWithKeys(
                fn ($value, $key) => [$key => $value],
            )->all(),
        ];

        $process = $this->createServerProcess(
            $host,
            $port,
            $this->getScriptPath(
                $path->getBasePath($script),
            ),
            $path->getBasePath(),
            $environments,
        );

        $process->start(
            /**
             * @param integer|string $type
             * @param string $buffer
             */
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );

        while ($process->isRunning()) {
            usleep(500 * 1000);
        }

        $status = $process->getExitCode();

        return $status ?? self::SUCCESS;
    }

    /**
     * Get the process instance of the server process.
     *
     * @param string $host
     * @param string $port
     * @param string $script
     * @param string $currentDirectory
     * @param mixed[] $environments
     * @return Process
     */
    private function createServerProcess(
        string $host,
        string $port,
        string $script,
        string $currentDirectory,
        array $environments,
    ): Process {
        $finder = new PhpExecutableFinder();

        if (false === $binary = $finder->find()) {
            throw new RuntimeException('Unable to find PHP binary to run server.');
        }

        return new Process(
            command: [
                $binary,
                '-S',
                $host . ':' . $port,
                $script,
            ],
            cwd: $currentDirectory,
            env: $environments,
        );
    }

    /**
     * Get script path.
     * If the specified script file does not exist, return the default script file path.
     *
     * @param string $script
     * @return string
     */
    private function getScriptPath(string $script): string
    {
        return file_exists($script)
            ? $script
            : $this->getDefaultScriptPath();
    }

    /**
     * Get default script path.
     *
     * @return string
     */
    private function getDefaultScriptPath(): string
    {
        return __DIR__ . '/../../../public/index.php';
    }
}
