<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Takemo101\Chubby\Console\Command\CallableCommand;
use Takemo101\Chubby\Console\CommandCollection;
use Takemo101\Chubby\Console\CommandResolver;
use Takemo101\Chubby\Console\SymfonyConsoleAdapter;
use Tests\AppTestCase;

describe(
    'console',
    function () {
        test(
            'Create an instance of SymfonyConsoleAdapter and execute a command',
            function () {
                /** @var AppTestCase $this */

                $app = $this->getContainer()->get(Application::class);

                $name = 'test';

                $console = new SymfonyConsoleAdapter(
                    $app,
                    new CommandCollection(
                        CallableCommand::from(fn () => CallableCommand::SUCCESS)
                            ->setName($name),
                    ),
                    new CommandResolver($this->getContainer()),
                );

                $command = $console->findCommand($name);

                $commandTester = new CommandTester($command);

                $commandTester->execute([]);

                $commandTester->assertCommandIsSuccessful();
            },
        );

        test(
            'Run a command to display the application version',
            function () {
                /** @var AppTestCase $this */

                $tester = $this->command('version');

                $tester->assertCommandIsSuccessful();
            },
        );

        test(
            'Add commands to SymfonyConsoleAdapter and execute them',
            function (Command $command) {
                /** @var AppTestCase $this */

                $this->getConsole()->addCommand($command);

                $name = $command->getName();

                $tester = $this->command($name);

                $tester->assertCommandIsSuccessful();
            }
        )->with([
            CallableCommand::from(
                fn () => CallableCommand::SUCCESS,
                'test01'
            ),
            CallableCommand::from(
                fn () => CallableCommand::SUCCESS,
                'test02'
            ),
            CallableCommand::from(
                fn () => CallableCommand::SUCCESS,
                'test03'
            ),
        ]);
    }
)->group('console');
