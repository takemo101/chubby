<?php

namespace Takemo101\Chubby;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Console\SymfonyConsoleAdapter;
use Takemo101\Chubby\Support\AbstractRunner;

/**
 * Execute Console processing by Symfony application.
 */
class Console extends AbstractRunner
{
    /**
     * Create an console instance.
     *
     * @return SymfonyConsoleAdapter
     */
    private function getConsole(): SymfonyConsoleAdapter
    {
        $this->getApp()->boot();

        /** @var SymfonyConsoleAdapter */
        $console = $this->getApp()->get(
            SymfonyConsoleAdapter::class,
        );

        return $console;
    }

    /**
     * Add command class instance.
     *
     * @param class-string<Command>|object ...$commands
     * @return self
     */
    public function addCommand(string|object ...$commands): self
    {
        $this->getConsole()->addCommand(...$commands);

        return $this;
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return void
     */
    public function run(
        ?InputInterface $input = null,
        ?OutputInterface $output = null,
    ): void {
        $status = $this->getConsole()->run(
            input: $input,
            output: $output,
        );

        exit($status);
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
        return $this->getConsole()->handle(
            input: $input,
            output: $output,
        );
    }

    /**
     * Create a simple instance with only Console functionality available from options
     *
     * @param ApplicationOption|null $option
     * @return self
     */
    public static function createSimple(
        ?ApplicationOption $option = null,
    ): self {
        return new self(
            ApplicationBuilder::fromOption($option)
                ->addConsole()
                ->getApplication(),
        );
    }
}
