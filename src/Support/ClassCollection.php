<?php

namespace Takemo101\Chubby\Support;

use stdClass;
use RuntimeException;

/**
 * Class or object collection.
 *
 * @template T of object
 */
abstract class ClassCollection
{
    /** @var class-string<T> */
    public const Type = stdClass::class;

    /** @var array<class-string<T>|T> */
    private array $classes = [];

    /**
     * constructor
     *
     * @param class-string<T>|T ...$classes
     */
    final public function __construct(
        string|object ...$classes,
    ) {
        $this->add(...$classes);
    }

    /**
     * Set a class name or object of the specified type.
     * Overwrite all classes.
     *
     * @param class-string<T>|T ...$classes
     * @return static
     * @throws RuntimeException
     */
    public function set(string|object ...$classes): static
    {
        $this->clear();
        $this->add(...$classes);

        return $this;
    }

    /**
     * Adds a class name or object of the specified type
     *
     * @param class-string<T>|T ...$classes
     * @return static
     * @throws RuntimeException
     */
    public function add(string|object ...$classes): static
    {
        /** @var array<class-string<T>|T> */
        $_classes = [];

        foreach ($classes as $class) {
            if (is_string($class) && !class_exists($class)) {
                throw new RuntimeException(
                    sprintf(
                        'Class "%s" does not exist.',
                        $class,
                    ),
                );
            }

            if (!(
                is_a($class, static::Type, true) // @phpstan-ignore-line
                || is_subclass_of($class, static::Type, true)
            )) {
                throw new RuntimeException(
                    sprintf(
                        'Class "%s" is not a subclass of "%s".',
                        is_string($class)
                            ? $class
                            : get_class($class),
                        static::Type,
                    ),
                );
            }

            $_classes[] = $class;
        }

        $this->classes = [
            ...$this->classes,
            ...$_classes,
        ];

        return $this;
    }

    /**
     * Remove a class name or object of the specified type
     *
     * @param class-string<T> $class
     * @return static
     * @throws RuntimeException
     */
    public function remove(string $class): static
    {
        if (!(
            class_exists($class)
            || interface_exists($class)
        )) {
            throw new RuntimeException(
                sprintf(
                    'Class "%s" does not exist.',
                    $class,
                ),
            );
        }

        $this->classes = array_filter(
            $this->classes,
            fn ($item) => !(
                is_a($item, $class, true) // @phpstan-ignore-line
                || is_subclass_of($item, $class, true)
            ),
        );

        return $this;
    }

    /**
     * Get all.
     *
     * @return array<class-string<T>|T>
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
