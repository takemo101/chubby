<?php

namespace Takemo101\Chubby\Test;

use PHPUnit\Framework\TestCase;
use Takemo101\Chubby\ApplicationContainer;
use RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Takemo101\Chubby\Console\SymfonyConsoleAdapter;

/**
 * @method ApplicationContainer getContainer()
 *
 * @mixin TestCase|HasContainerTest
 */
trait HasConsoleTest
{
    /**
     * @var SymfonyConsoleAdapter
     */
    private SymfonyConsoleAdapter $console;

    /**
     * Set slim http adapter.
     *
     * @return void
     */
    protected function setUpConsole(): void
    {
        $this->console = $this->getContainer()->get(SymfonyConsoleAdapter::class);
    }

    /**
     * Get symfony console adapter.
     *
     * @return SymfonyConsoleAdapter
     */
    protected function getConsole(): SymfonyConsoleAdapter
    {
        return isset($this->console)
            ? $this->console
            : throw new RuntimeException('Console is not set.');
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
