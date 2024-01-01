<?php

namespace Takemo101\Chubby\Event;

readonly class PrioritizedListener
{
    public const DefaultPriority = 50;

    /**
     * constructor
     *
     * @param class-string|object $classOrObject
     * @param string $method
     * @param int $priority
     */
    public function __construct(
        private string|object $classOrObject,
        private string $method = '__invoke',
        private int $priority = self::DefaultPriority,
    ) {
        //
    }

    /**
     * @return class-string
     */
    public function getListenerClass(): string
    {
        return is_object($this->classOrObject)
            ? get_class($this->classOrObject)
            : $this->classOrObject;
    }

    /**
     * @return class-string|object
     */
    public function getListenerClassOrObject(): string|object
    {
        return $this->classOrObject;
    }

    /**
     * @return string
     */
    public function getListenerMethod(): string
    {
        return $this->method;
    }

    /**
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
