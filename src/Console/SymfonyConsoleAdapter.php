<?php

namespace Takemo101\Chubby\Console;

use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

final class SymfonyConsoleAdapter
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
        private readonly CommandResolver $resolver,
    ) {
        //
    }

    public function addCommand(string|object ...$commands): static
    {
        foreach ($commands as $command) {
            $resolved = $this->resolver->resolve($command);

            if (!$resolved) {
                $name = $this->getClassName($command);

                throw new RuntimeException("{$name} is not command class");
            }

            $this->application->add($resolved);
        }

        return $this;
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
        return $this->application->doRun($input, $output);
    }

    /**
     * Get class name
     *
     * @param string|object $class
     * @return string
     */
    private function getClassName(string|object $class): string
    {
        return is_object($class) ? get_class($class) : $class;
    }
}
