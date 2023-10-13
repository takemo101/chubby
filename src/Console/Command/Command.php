<?php

namespace Takemo101\Chubby\Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LogicException;
use Takemo101\Chubby\Application;

/**
 * Abstract command.
 */
abstract class Command extends BaseCommand
{
    /**
     * @var InputInterface|null
     */
    private ?InputInterface $input = null;

    /**
     * @var OutputInterface|null
     */
    private ?OutputInterface $output = null;

    /**
     * @param string|null $name
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        protected readonly Application $app,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    /**
     * execute command process
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (method_exists($this, 'handle')) {
            $this->input = $input;
            $this->output = $output;

            $this->app->set(InputInterface::class, $input);
            $this->app->set(OutputInterface::class, $output);

            /** @var integer */
            $exitCode = $this->app->call([$this, 'handle'], [
                'input' => $input,
                'output' => $output,
            ]);

            return $exitCode;
        }

        return self::SUCCESS;
    }

    /**
     * Get input.
     *
     * @return InputInterface
     */
    protected function input(): InputInterface
    {
        if ($this->input === null) {
            throw new LogicException('input is not set!');
        }

        return $this->input;
    }

    /**
     * Get output.
     *
     * @return OutputInterface
     */
    protected function output(): OutputInterface
    {
        if ($this->output === null) {
            throw new LogicException('output is not set!');
        }

        return $this->output;
    }
}
