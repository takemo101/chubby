<?php

namespace Takemo101\Chubby\Event;

use InvalidArgumentException;
use Takemo101\Chubby\Contract\Arrayable;

/**
 * Event register
 *
 * @implements Arrayable<class-string,PrioritizedListener[]>
 */
class EventRegister implements Arrayable
{
    /**
     * constructor
     *
     * @param array<class-string,PrioritizedListener[]> $map
     */
    public function __construct(
        private array $map = [],
    ) {
        //
    }

    /**
     * Register a listener for the event
     *
     * @template T of object
     *
     * @param class-string<T> $event
     * @param class-string<EventListener<T>>|callable(T):void $classOrCallable
     * @param int $priority
     * @return self
     * @throws InvalidArgumentException
     */
    public function on(
        string $event,
        string|callable $classOrCallable,
        int $priority = PrioritizedListener::DefaultPriority,
    ): self {
        /**
         * Existing listener
         *
         * @var PrioritizedListener[]
         */
        $existing = $this->map[$event] ?? [];

        $prioritized = PrioritizedListener::from(
            $classOrCallable, // @phpstan-ignore-line
            $priority,
        );

        $existing[] = $prioritized;

        $this->map[$event] = $existing;

        return $this;
    }

    /**
     * Get a listener for the event
     *
     * @param class-string $event
     * @return PrioritizedListener[]
     */
    public function get(string $event): array
    {
        return $this->map[$event] ?? [];
    }

    /**
     * Check if the event has a listener
     *
     * @param class-string $event
     * @return bool
     */
    public function has(string $event): bool
    {
        return isset($this->map[$event]);
    }

    /**
     * Remove a listener for the event
     *
     * @param class-string $event
     * @return self
     */
    public function remove(string $event): self
    {
        unset($this->map[$event]);

        return $this;
    }

    /**
     * Get all
     *
     * @return array<class-string,PrioritizedListener[]>
     */
    public function toArray(): array
    {
        return $this->map;
    }

    /**
     * Create a instance from array
     *
     * @param array<class-string,class-string<EventListener<object>>|class-string<EventListener<object>>[]> $listen
     * @return self
     */
    public static function fromArray(array $listen): self
    {
        $instance = new self();

        foreach ($listen as $event => $class) {
            $classes = is_array($class) ? $class : [$class];

            foreach ($classes as $i => $c) {
                $instance->on(
                    event: $event,
                    classOrCallable: $c,
                    priority: PrioritizedListener::DefaultPriority + $i,
                );
            }
        }

        return $instance;
    }
}
