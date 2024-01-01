<?php

namespace Takemo101\Chubby\Event;

use Takemo101\Chubby\Contract\Arrayable;
use InvalidArgumentException;

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
     * @param EventMapExtractor $extractor
     * @param array<class-string,PrioritizedListener[]> $map
     */
    public function __construct(
        private EventMapExtractor $extractor = new EventMapExtractor(),
        private array $map = [],
    ) {
        //
    }

    /**
     * Register a listener for the event
     * Give the names of classes and their instances that implement Closure and __invoke as a listener.
     * It is not possible to specify an array or function name as a Callable value.
     *
     * @param class-string|object $listener
     * @return self
     * @throws InvalidArgumentException
     */
    public function on(
        string|object $listener,
    ): self {
        $map = $this->extractor->extract($listener);

        foreach ($map as $event => $prioritized) {

            $this->listen($event, ...$prioritized);
        }

        return $this;
    }

    /**
     * Register a listener for the event with priority
     *
     * @param class-string $event
     * @param PrioritizedListener ...$listeners
     * @return self
     */
    public function listen(
        string $event,
        PrioritizedListener ...$listeners,
    ): self {

        $exists = $this->map[$event] ?? [];

        $this->map[$event] = [
            ...$exists,
            ...$listeners,
        ];

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
     * @param class-string[] $listen
     * @return self
     */
    public static function fromArray(array $listen): self
    {
        $instance = new self();

        foreach ($listen as $listener) {
            $instance->on($listener);
        }

        return $instance;
    }
}
