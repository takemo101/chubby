<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Symfony\Component\Console\Application as SymfonyConsole;
use Takemo101\Chubby\Console\Command\VersionCommand;
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
                    CommandResolver $resolver,
                    Hook $hook,
                ): SymfonyConsoleAdapter {
                    $console = new SymfonyConsoleAdapter(
                        application: $console,
                        resolver: $resolver,
                    );

                    $console->addCommand(VersionCommand::class);

                    $hook->doActionByObject($console);

                    return $console;
                }
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        //
    }
}
