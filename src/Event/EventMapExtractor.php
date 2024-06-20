<?php

namespace Takemo101\Chubby\Event;

use Takemo101\Chubby\Event\Attribute\AsEventListener;
use ReflectionClass;
use ReflectionAttribute;
use InvalidArgumentException;
use Takemo101\Chubby\Event\Exception\EventTypeInferenceException;

class EventMapExtractor
{
    /**
     * constructor
     *
     * @param EventTypeInferencer $inferencer
     */
    public function __construct(
        private EventTypeInferencer $inferencer = new EventTypeInferencer(),
    ) {
        //
    }

    /**
     * Extract the event map from the listener.
     *
     * @param class-string|object $listener
     * @return array<class-string,PrioritizedListener[]>
     * @throws InvalidArgumentException
     */
    public function extract(
        string|object $listener,
    ): array {
        if (is_string($listener) && !class_exists($listener)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Listener class %s does not exist',
                    $listener,
                ),
            );
        }

        $class = new ReflectionClass($listener);

        $attributes = $class->getAttributes(AsEventListener::class);

        return empty($attributes)
            ? $this->extractDefaultEventMap($class, $listener)
            : $this->extractEventMapFromAttributes($class, $attributes, $listener);
    }

    /**
     * Create a default event map.
     * Attributes are not used.
     *
     * @param ReflectionClass<object> $class
     * @param class-string|object $listener
     * @return array<class-string,PrioritizedListener[]>
     * @throws EventTypeInferenceException
     */
    private function extractDefaultEventMap(ReflectionClass $class, string|object $listener): array
    {
        $method = AsEventListener::DefaultMethod;

        $events = $this->inferencer->inference($class, $method);

        /** @var array<class-string,PrioritizedListener[]> */
        $map = [];

        foreach ($events as $event) {
            $map[$event] = [
                new PrioritizedListener(
                    classOrObject: $listener,
                    method: $method,
                ),
            ];
        }

        return $map;
    }

    /**
     * Create a event map from attribute.
     *
     * @param ReflectionClass<object> $class
     * @param ReflectionAttribute<AsEventListener>[] $attributes
     * @param class-string|object $listener
     * @return array<class-string|string,PrioritizedListener[]>
     * @throws EventTypeInferenceException

     */
    private function extractEventMapFromAttributes(
        ReflectionClass $class,
        array $attributes,
        string|object $listener,
    ): array {
        /** @var array<class-string,PrioritizedListener[]> */
        $map = [];

        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();

            $method = $instance->getMethod();

            $event = $instance->getEvent();

            $events = $event
                ? [$event]
                : $this->inferencer->inference($class, $method);

            foreach ($events as $event) {
                $map[$event] = [
                    new PrioritizedListener(
                        classOrObject: $listener,
                        method: $method,
                        priority: $instance->getPriority(),
                    ),
                ];
            }
        }

        return $map;
    }
}
