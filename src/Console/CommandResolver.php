<?php

namespace Takemo101\Chubby\Console;

use DI\FactoryInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Command resolution
 */
final class CommandResolver
{
    /**
     * constructor
     *
     * @param FactoryInterface $factory
     */
    public function __construct(
        private readonly FactoryInterface $factory,
    ) {
        //
    }

    /**
     * Resolves to command object.
     * If unresolvable, return null.
     *
     * @param class-string<Command>|object $command
     * @return Command|null
     */
    public function resolve(string|object $command): ?Command
    {
        return is_string($command)
            ? $this->getCommandObjectOr(
                $this->factory->make($command),
            )
            : $this->getCommandObjectOr($command);
    }

    /**
     * If it is a command object, return the object as is; if it is not a command object, return null.
     *
     * @param mixed $command
     * @return Command|null
     */
    private function getCommandObjectOr(mixed $command): ?Command
    {
        if (is_object($command) && $command instanceof Command) {
            return $command;
        }

        return null;
    }
}
