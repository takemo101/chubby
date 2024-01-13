<?php

namespace Takemo101\Chubby\Event;

use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Event\Exception\EventListenerResolveException;

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
     * @throws EventListenerResolveException
     */
    public function resolve(string $class): object
    {
        /** @var object */
        $listener = $this->container->get($class);

        if (!is_object($listener)) {
            throw EventListenerResolveException::notCallableOrObjectError($class);
        }

        return $listener;
    }
}
