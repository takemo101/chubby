<?php

namespace Takemo101\Chubby\Event;

use Psr\Container\ContainerInterface;

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
     * @param class-string<EventListener<object>> $class
     * @return EventListener<object>
     */
    public function resolve(string $class): EventListener
    {
        /** @var EventListener<object> */
        $listener = $this->container->get($class);

        return $listener;
    }
}
