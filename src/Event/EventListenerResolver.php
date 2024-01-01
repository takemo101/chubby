<?php

namespace Takemo101\Chubby\Event;

use Psr\Container\ContainerInterface;
use RuntimeException;

class EventListenerResolver
{
    /**
     * constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        private ContainerInterface $container,
    ) {
        //
    }

    /**
     * Resolve the listener.
     *
     * @param class-string $class
     * @return object
     */
    public function resolve(string $class): object
    {
        /** @var object */
        $listener = $this->container->get($class);

        if (!is_object($listener)) {
            throw new RuntimeException(
                sprintf(
                    'The listener %s is not a callable or object.',
                    $class,
                ),
            );
        }

        return $listener;
    }
}
