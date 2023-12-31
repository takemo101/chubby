<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

use SplPriorityQueue as PriorityQueue;

;

class EventListenerProvider implements ListenerProviderInterface
{
    public function __construct(
        private EventRegister $register,
        private EventListenerResolver $resolver,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     *
     * @template T of object
     *
     * @param T $event
     * @return iterable<callable(T):void>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $class = $event instanceof AliasableEvent
            ? $event->getAlias()
            : get_class($event);

        $priorities = $this->register->get($class);

        /** @var PriorityQueue<integer,callable(T):void> */
        $queue = new PriorityQueue();

        foreach ($priorities as $prioritized) {

            $listener = $prioritized->listener;

            /** @var callable(T):void */
            $callable = is_string($listener)
                ? $this->resolver->resolve($listener)
                : $listener;

            $queue->insert($callable, $prioritized->priority);
        }

        return $queue;
    }
}
