<?php

namespace Takemo101\Chubby\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Support\AbstractRunner;

/**
 * Execute Console processing by Symfony application.
 */
final readonly class Console extends AbstractRunner
{
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
        $this->getApp()->boot();

        /** @var SymfonyConsoleAdapter */
        $console = $this->getApp()->get(
            SymfonyConsoleAdapter::class,
        );

        $status = $console->run(
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
        $this->getApp()->boot();

        /** @var SymfonyConsoleAdapter */
        $console = $this->getApp()->get(
            SymfonyConsoleAdapter::class,
        );

        return $console->handle(
            input: $input,
            output: $output,
        );
    }
}
