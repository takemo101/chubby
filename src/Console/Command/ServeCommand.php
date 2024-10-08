<?php

namespace Takemo101\Chubby\Console\Command;

use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Takemo101\Chubby\Support\Environment;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Takemo101\Chubby\Clock\Clock;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;

/**
 * Use PHP's built-in web server.
 * reference: https://github.com/guiwoda/laravel-framework/blob/master/src/Illuminate/Foundation/Console/ServeCommand.php
 */
#[AsCommand(
    name: 'serve',
    description: 'Use PHP\'s built-in web server.',
)]
class ServeCommand extends Command
{
    /**
     * @var string Built-in server environment name.
     */
    public const BuiltInServerEnvironment = 'BUILT_IN_SERVER';

    /**
     * @var LocalFilesystem
     */
    private LocalFilesystem $filesystem;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        /** @var LocalFilesystem */
        $filesystem = $this->getContainer()->get(LocalFilesystem::class);

        $this->filesystem = $filesystem;

        /** @var Environment */
        $env = $this->getContainer()->get(Environment::class);

        $port = $env->get('SERVER_PORT', '8080');
        $host = $env->get('SERVER_HOST', 'localhost');
        $script = $env->get('SERVER_SCRIPT', '/index.php');

        $this
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
                description: 'PHP script file or document root',
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
        Clock $clock,
    ) {
        /** @var string */
        $port = $input->getOption('port');
        /** @var string */
        $host = $input->getOption('host');
        /** @var string */
        $scriptOrDocumentRoot = $input->getOption('script');

        $environments = [
            self::BuiltInServerEnvironment => true,
            'APP_BASE_PATH' => $path->getBasePath(),
            ...collect($_ENV)->mapWithKeys(
                fn ($value, $key) => [$key => $value],
            )->all(),
        ];

        $process = $this->createServerProcess(
            $host,
            $port,
            $this->getScriptPath(
                $path->getBasePath($scriptOrDocumentRoot),
                $this->getDefaultScriptPaths($path),
            ),
            $path->getBasePath(),
            $environments,
        );

        /** @var integer */
        $workers = env('PHP_CLI_SERVER_WORKERS', 1);

        $process->start(new ServeProcessOutputHandler(
            output: $output,
            isEnabledCliServerWorkers: $workers > 1,
            startedAt: $clock->now(),
        ));

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
            command: array_filter([
                $binary,
                '-S',
                $host . ':' . $port,
                $this->filesystem->isDirectory($script) ? '-t' : null,
                $script,
            ]),
            cwd: $currentDirectory,
            env: $environments,
        );
    }

    /**
     * Get script path.
     * If the specified script file does not exist, return the default script file path.
     *
     * @param string $script
     * @param string[] $defaultPaths
     * @return string
     */
    private function getScriptPath(
        string $script,
        array $defaultPaths,
    ): string {
        /** @var string[] */
        $paths = array_unique([
            $script,
            ...$defaultPaths,
        ]);

        foreach ($paths as $path) {
            if ($this->filesystem->exists($path)) {
                return $path;
            }
        }

        throw new RuntimeException('Unable to find the script file.');
    }

    /**
     * Get default script path.
     *
     * @param ApplicationPath $path
     * @return string[]
     */
    private function getDefaultScriptPaths(ApplicationPath $path): array
    {
        return [
            $path->getBasePath('/public/index.php'),
            $path->getBasePath('/index.php'),
            $path->getSettingPath('/index.php'),
        ];
    }
}
