<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * constructor
     *
     * @param ListenerProviderInterface $provider
     */
    public function __construct(
        private ListenerProviderInterface $provider,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     *
     * @return object
     */
    public function dispatch(object $event)
    {
        foreach ($this->provider->getListenersForEvent($event) as $listener) {
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
