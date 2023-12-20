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
     * @param class-string<Command>|Command $command
     * @return Command
     */
    public function resolve(string|Command $command): Command
    {
        if (is_string($command)) {
            /** @var Command */
            $object = $this->container->make($command);
        } else {
            $object = $command;
        }

        if ($object instanceof ContainerInjectable) {
            $object->setContainer($this->container);
        }

        return $object;
    }
}
