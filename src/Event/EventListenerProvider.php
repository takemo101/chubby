<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\ListenerProviderInterface;
use Takemo101\Chubby\Event\Attribute\AsEvent;
use Closure;
use ReflectionAttribute;
use ReflectionClass;
use RuntimeException;
use SplPriorityQueue as PriorityQueue;

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
     * @return iterable<callable(object):void>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $attributes = (new ReflectionClass($event))
            ->getAttributes(AsEvent::class);

        /** @var class-string[] */
        $eventClasses = empty($attributes)
            ? [get_class($event)]
            : array_map(
                fn (ReflectionAttribute $attribute) => $attribute->newInstance()->getAlias(),
                $attributes,
            );

        return $this->createListenerQueue($eventClasses);
    }

    /**
     * Create a queue of listeners.
     *
     * @param class-string[] $eventClasses
     * @return iterable<callable(object):void>
     */
    private function createListenerQueue(
        array $eventClasses,
    ): iterable {

        /** @var PriorityQueue<integer,Closure(object):void> */
        $queue = new PriorityQueue();

        foreach ($eventClasses as $eventClass) {
            $priorities = $this->register->get($eventClass);

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
                    throw new RuntimeException(
                        sprintf(
                            'The listener %s is not a callable or object.',
                            $prioritized->getListenerClass(),
                        ),
                    );
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