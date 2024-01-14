<?php

namespace Takemo101\Chubby\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Console\SymfonyConsole;
use LogicException;

/**
 * @method ApplicationContainer getContainer()
 *
 * @mixin TestCase|HasContainerTest
 */
trait HasConsoleTest
{
    /**
     * @var SymfonyConsole
     */
    private SymfonyConsole $console;

    /**
     * Set slim http adapter.
     *
     * @return void
     */
    protected function setUpConsole(): void
    {
        $this->console = $this->getContainer()->get(SymfonyConsole::class);
    }

    /**
     * Get symfony console adapter.
     *
     * @return SymfonyConsole
     */
    protected function getConsole(): SymfonyConsole
    {
        return isset($this->console)
            ? $this->console
            : throw new LogicException('Console is not set.');
    }

    /**
     * Test run a command and get an assertable CommandTester.
     *
     * @param string $name
     * @param array<string,mixed> $input
     * @param array<string,mixed> $options
     * @return CommandTester
     */
    public function command(
        string $name,
        array $input = [],
        array $options = [],
    ): CommandTester {
        $command = $this->getConsole()->findCommand($name);

        $commandTester = new CommandTester($command);

        $commandTester->execute($input, $options);

        return $commandTester;
    }
}
