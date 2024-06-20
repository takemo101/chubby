<?php

namespace Takemo101\Chubby\Event;

use Takemo101\Chubby\Event\Attribute\AsEvent;
use Closure;
use ReflectionAttribute;
use ReflectionClass;
use SplPriorityQueue as PriorityQueue;
use Takemo101\Chubby\Event\Exception\EventListenerResolveException;

class EventListenerProvider implements ListenerProvider
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
     * @return iterable<callable(object):void>
     * @throws EventListenerResolveException
     */
    public function getListenersForEvent(object $event): iterable
    {
        $attributes = (new ReflectionClass($event))
            ->getAttributes(AsEvent::class);

        /** @var class-string[] */
        $eventClasses = empty($attributes)
            ? []
            : array_map(
                fn (ReflectionAttribute $attribute) => $attribute->newInstance()->getAlias(),
                $attributes,
            );

        $eventClasses[] = get_class($event);

        $eventClasses = array_unique($eventClasses);

        return $this->createListenerQueue($eventClasses);
    }

    /**
     * {@inheritDoc}
     */
    public function getListeners(string $eventName): iterable
    {
        return $this->createListenerQueue([$eventName]);
    }

    /**
     * Create a queue of listeners.
     *
     * @param array<class-string|string> $eventNames Event names or class names
     * @return iterable<callable(object):void>
     * @throws EventListenerResolveException
     */
    private function createListenerQueue(
        array $eventNames,
    ): iterable {

        /** @var PriorityQueue<integer,Closure(object):void> */
        $queue = new PriorityQueue();

        foreach ($eventNames as $eventName) {
            $priorities = $this->register->get($eventName);

            foreach ($priorities as $prioritized) {

                $listener = $prioritized->getListenerClassOrObject();

                $object = is_object($listener)
                    ? $listener
                    : $this->resolver->resolve($listener);

                /** @var callable(object):void */
                $callable = [
                    $object,
                    $prioritized->getListenerMethod(),
                ];

                if (!is_callable($callable)) {
                    throw EventListenerResolveException::notCallableOrObjectError($prioritized->getListenerClass());
                }

                $queue->insert(
                    Closure::fromCallable($callable),
                    $prioritized->getPriority(),
                );
            }
        }

        return $queue;
    }
}
