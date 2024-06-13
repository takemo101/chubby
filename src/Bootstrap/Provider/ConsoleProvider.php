<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Console\Command\LogCleanCommand;
use Takemo101\Chubby\Console\Command\ServeCommand;
use Takemo101\Chubby\Console\Command\VersionCommand;
use Takemo101\Chubby\Console\CommandCollection;
use Takemo101\Chubby\Console\CommandResolver;
use Takemo101\Chubby\Console\SymfonyConsole;
use Takemo101\Chubby\Hook\Hook;

/**
 * Console application related.
 */
class ConsoleProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'console';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions->add(
            [
                Console::class => function (): Console {
                    $console = new Console(
                        Application::Name,
                        Application::Version,
                    );

                    $console->setAutoExit(false);

                    return $console;
                },
                SymfonyConsole::class => function (
                    Console $console,
                    CommandCollection $commands,
                    CommandResolver $resolver,
                    Hook $hook,
                ): SymfonyConsole {
                    $adapter = new SymfonyConsole(
                        application: $console,
                        commands: $commands,
                        resolver: $resolver,
                    );

                    $adapter->addCommand(
                        VersionCommand::class,
                        ServeCommand::class,
                        LogCleanCommand::class,
                    );

                    $hook->doTyped($adapter, true);

                    return $adapter;
                },
                CommandCollection::class => function (
                    ConfigRepository $config,
                    Hook $hook,
                ): CommandCollection {

                    /** @var class-string<SymfonyCommand>[] */
                    $classes = $config->get('console.commands', []);

                    $commands = new CommandCollection(...$classes);

                    $hook->doTyped($commands, true);

                    return $commands;
                }
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        //
    }
}
