<?php

namespace Takemo101\Chubby\Event;

use InvalidArgumentException;

readonly class PrioritizedListener
{
    public const DefaultPriority = 50;

    /**
     * @var class-string<EventListener<object>>|EventListener<object>
     */
    public string|EventListener $listener;

    /**
     * constructor
     *
     * @param class-string<EventListener<object>>|EventListener<object> $listener
     * @param int $priority
     */
    public function __construct(
        string|EventListener $listener,
        public int $priority = self::DefaultPriority,
    ) {
        $this->listener = $listener;
    }

    /**
     * Create a listener to register
     *
     * @param class-string<EventListener<object>>|callable(object):void $classOrCallable
     * @param int $priority
     * @return self
     * @throws InvalidArgumentException
     */
    public static function from(
        string|callable $classOrCallable,
        int $priority = self::DefaultPriority,
    ): self {

        if ($classOrCallable instanceof EventListener) {
            return new self(
                listener: $classOrCallable,
                priority: $priority,
            );
        }

        if (is_callable($classOrCallable)) {
            $listener = $classOrCallable instanceof EventListener
                ? $classOrCallable
                : ClosureListener::from($classOrCallable);

            return new self(
                listener: $listener,
                priority: $priority,
            );
        }

        if (!(
            class_exists($classOrCallable)
            && is_subclass_of($classOrCallable, EventListener::class, true)
        )) {
            throw new InvalidArgumentException(
                sprintf(
                    'Listener class %s does not exist',
                    $classOrCallable,
                ),
            );
        }

        return new self(
            listener: $classOrCallable,
            priority: $priority,
        );
    }
}
