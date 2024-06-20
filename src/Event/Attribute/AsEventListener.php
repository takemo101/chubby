<?php

namespace Takemo101\Chubby\Event\Attribute;

use Attribute;
use Takemo101\Chubby\Event\PrioritizedListener;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class AsEventListener
{
    public const DefaultMethod = '__invoke';

    public const DefaultPriority = PrioritizedListener::DefaultPriority;

    /**
     * constructor
     *
     * @param class-string|string|null $event Event class name or event name.
     * @param string|null $method
     * @param int|null $priority
     */
    public function __construct(
        private ?string $event = null,
        private ?string $method = null,
        private ?int $priority = null,
    ) {
        //
    }

    /**
     * @return class-string|string|null
     */
    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method ?? self::DefaultMethod;
    }

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority ?? self::DefaultPriority;
    }
}
