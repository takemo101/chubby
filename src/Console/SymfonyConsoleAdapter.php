<?php

namespace Takemo101\Chubby\Console;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class SymfonyConsoleAdapter
{
    /**
     * constructor
     *
     * @param Application $application
     * @param CommandCollection $commands
     * @param CommandResolver $resolver
     */
    public function __construct(
        private readonly Application $application,
        private readonly CommandCollection $commands,
        private readonly CommandResolver $resolver,
    ) {
        //
    }

    /**
     * Add Symfony command.
     * Add a class string or instance that implements the command.
     *
     * @param class-string<Command>|Command ...$commands
     * @return static
     */
    public function addCommand(string|Command ...$commands): static
    {
        $this->commands->add(...$commands);

        return $this;
    }

    /**
     * Find Symfony command.
     *
     * @param string $name
     * @return Command
     * @throws CommandNotFoundException
     */
    public function findCommand(string $name): Command
    {
        $this->submitCommands();

        return $this->application->find($name);
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return integer
     */
    public function run(
        ?InputInterface $input = null,
        ?OutputInterface $output = null,
    ): int {
        $this->submitCommands();

        return $this->application->run($input, $output);
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return integer
     */
    public function handle(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->submitCommands();

        return $this->application->doRun($input, $output);
    }

    /**
     * Submit commands
     *
     * @return void
     */
    private function submitCommands(): void
    {
        foreach ($this->commands->classes() as $command) {
            $resolved = $this->resolver->resolve($command);

            $this->application->add($resolved);
        }

        $this->commands->clear();
    }
}
