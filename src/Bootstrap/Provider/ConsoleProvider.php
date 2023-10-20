<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Symfony\Component\Console\Application as SymfonyConsole;
use Takemo101\Chubby\Console\Command\ServeCommand;
use Takemo101\Chubby\Console\Command\VersionCommand;
use Takemo101\Chubby\Console\CommandCollection;
use Takemo101\Chubby\Console\CommandResolver;
use Takemo101\Chubby\Console\SymfonyConsoleAdapter;
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
                SymfonyConsole::class => function (): SymfonyConsole {
                    $console = new SymfonyConsole(
                        Application::Name,
                        Application::Version,
                    );

                    $console->setAutoExit(false);

                    return $console;
                },
                SymfonyConsoleAdapter::class => function (
                    SymfonyConsole $console,
                    CommandCollection $commands,
                    CommandResolver $resolver,
                    Hook $hook,
                ): SymfonyConsoleAdapter {
                    $adapter = new SymfonyConsoleAdapter(
                        application: $console,
                        commands: $commands,
                        resolver: $resolver,
                    );

                    $adapter->addCommand(
                        VersionCommand::class,
                        ServeCommand::class,
                    );

                    $hook->doByObject($adapter);

                    return $adapter;
                },
                CommandCollection::class => function (
                    Hook $hook,
                ): CommandCollection {
                    $commands = CommandCollection::empty();

                    $hook->doByObject($commands);

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
