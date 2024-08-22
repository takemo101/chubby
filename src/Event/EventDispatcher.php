<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Takemo101\Chubby\Event\Exception\EventListenerHandlingExceptions;
use Throwable;

class EventDispatcher implements SymfonyEventDispatcherInterface
{
    /**
     * constructor
     *
     * @param ListenerProvider $provider
     */
    public function __construct(
        private ListenerProvider $provider,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     *
     * @throws EventListenerHandlingExceptions
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        $listeners = is_string($eventName)
            ? $this->provider->getListeners($eventName)
            : $this->provider->getListenersForEvent($event);

        // If an exception occurs during the listener call, throw the exception.
        if ($exceptions = $this->handleListeners($listeners, $event)) {
            throw $exceptions;
        }

        return $event;
    }

    /**
     * Executes all listener functions in the listener array and returns an exception if any occur.
     * Returns null if no exceptions occur.
     *
     * If an exception occurs in a specific listener, it interrupts the execution of that listener and proceeds to the next one.
     *
     * @param iterable<callable> $listeners
     * @param object $event
     * @return EventListenerHandlingExceptions|null
     */
    private function handleListeners(iterable $listeners, object $event): ?EventListenerHandlingExceptions
    {
        $throwables = [];

        foreach ($listeners as $listener) {
            try {
                call_user_func($listener, $event);

                if (
                    $event instanceof StoppableEventInterface
                    && $event->isPropagationStopped()
                ) {
                    break;
                }
            } catch (Throwable $e) {
                $throwables[] = $e;
            }
        }

        return EventListenerHandlingExceptions::createIfNotEmpty(...$throwables);
    }
}
