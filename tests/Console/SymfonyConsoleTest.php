<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Takemo101\Chubby\Application as ChubbyApplication;
use Takemo101\Chubby\Console\Command\ClosureCommand;
use Takemo101\Chubby\Console\CommandCollection;
use Takemo101\Chubby\Console\CommandResolver;
use Takemo101\Chubby\Console\SymfonyConsole;
use Tests\AppTestCase;

describe(
    'console',
    function () {
        test(
            'Create an instance of SymfonyConsole and execute a command',
            function () {
                /** @var AppTestCase $this */

                $app = $this->getContainer()->get(Application::class);

                $name = 'test';

                $console = new SymfonyConsole(
                    $app,
                    new CommandCollection(
                        ClosureCommand::from(fn () => ClosureCommand::SUCCESS)
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

                expect($tester->getDisplay())->toContain(
                    ChubbyApplication::Version,
                );
            },
        );

        test(
            'Add commands to SymfonyConsole and execute them',
            function (Command $command) {
                /** @var AppTestCase $this */

                $this->getConsole()->addCommand($command);

                $name = $command->getName();

                $tester = $this->command($name);

                $tester->assertCommandIsSuccessful();
            }
        )->with([
            ClosureCommand::from(
                fn () => ClosureCommand::SUCCESS,
                'test01'
            ),
            ClosureCommand::from(
                fn () => ClosureCommand::SUCCESS,
                'test02'
            ),
            ClosureCommand::from(
                fn () => ClosureCommand::SUCCESS,
                'test03'
            ),
        ]);
    }
)->group('console');
