<?php

namespace Takemo101\Chubby\Console;

use Symfony\Component\Console\Command\Command;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Contract\ContainerInjectable;

/**
 * Command resolution
 */
class CommandResolver
{
    /**
     * constructor
     *
     * @param ApplicationContainer $container
     */
    public function __construct(
        private readonly ApplicationContainer $container,
    ) {
        //
    }

    /**
     * Resolves to command object.
     * If unresolvable, return null.
     *
     * @param class-string<Command>|object|callable $command
     * @return Command|null
     */
    public function resolve(string|object|callable $command): ?Command
    {
        $object = $this->getCommandObjectOr(
            is_string($command)
                ? $this->container->make($command)
                : $command
        );

        if (!$object) {
            return null;
        }

        if ($object instanceof ContainerInjectable) {
            $object->setContainer($this->container);
        }

        return $object;
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
