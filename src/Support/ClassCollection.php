<?php

namespace Takemo101\Chubby\Support;

/**
 * Class or object collection.
 *
 * @template T
 */
abstract class ClassCollection
{
    /** @var array<class-string|object> */
    private array $classes;

    /**
     * constructor
     *
     * @param class-string<T>|object<T> ...$classes
     */
    final public function __construct(
        string|object ...$classes,
    ) {
        $this->classes = $classes;
    }

    /**
     * Adds a class name or object of the specified type
     *
     * @param class-string<T>|object<T> ...$classes
     * @return static
     */
    public function add(string|object ...$classes): static
    {
        /** @var array<class-string<T>|object<T>> */
        $_classes = [
            ...$this->classes,
            ...$classes,
        ];

        $this->classes = $_classes;

        return $this;
    }

    /**
     * Get all.
     *
     * @return array<class-string<T>|object<T>>
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * Clear and retrieve added classes
     *
     * @return void
     */
    public function clear(): void
    {
        $this->classes = [];
    }

    /**
     * Create an empty collection.
     *
     * @return static
     */
    public static function empty(): static
    {
        return new static();
    }
}
