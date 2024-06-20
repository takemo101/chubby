<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

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
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        $listeners = is_string($eventName)
            ? $this->provider->getListeners($eventName)
            : $this->provider->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            call_user_func($listener, $event);

            if (
                $event instanceof StoppableEventInterface
                && $event->isPropagationStopped()
            ) {
                break;
            }
        }

        return $event;
    }
}
