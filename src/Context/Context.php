<?php

namespace Takemo101\Chubby\Context;

use InvalidArgumentException;

/**
 * Getting and setting values of context.
 */
class Context
{
    /**
     * @var array<string,ContextValue>
     */
    private array $values = [];

    /**
     * constructor
     *
     * @param array<string,mixed> $values
     */
    public function __construct(
        array $values = [],
    ) {
        foreach ($values as $id => $value) {
            $this->set($id, $value);
        }

        $this->set(self::class, $this);

        if (!$this->has(static::class)) {
            $this->setAliases(self::class, [static::class]);
        }
    }

    /**
     * Sets a value that depends on the request context.
     *
     * @param string $id
     * @param mixed $value
     * @return self
     */
    public function set(string $id, mixed $value): self
    {
        // If the value already exists, update it.
        if ($object = $this->values[$id] ?? false) {
            $object->update($value);
        } else {
            $this->values[$id] = new ContextValue($value);
        }

        return $this;
    }

    /**
     * Sets a value that depends on the request context and matches the specified object type.
     *
     * @param object $object
     * @return self
     */
    public function setTyped(object $object): self
    {
        return $this->set(get_class($object), $object);
    }

    /**
     * Gets a value that depends on the request context.
     *
     * @param string $id
     * @param mixed $default
     * @return mixed
     */
    public function get(string $id, mixed $default = null): mixed
    {
        return array_key_exists($id, $this->values)
            ? $this->values[$id]->value()
            : $default;
    }

    /**
     * Gets a value that depends on the request context and matches the specified object type.
     *
     * @template T of object
     *
     * @param class-string<T> $type
     * @return T
     * @throws InvalidArgumentException If the value does not match the specified object type.
     */
    public function getTyped(string $type): mixed
    {
        if (!array_key_exists($type, $this->values)) {
            throw new InvalidArgumentException("The value with the specified identifier does not exist: {$type}");
        }

        $value = $this->values[$type]->value();

        if ($value instanceof $type) {
            return $value;
        }

        throw new InvalidArgumentException("The value does not match the specified object type: {$type}");
    }

    /**
     * Sets aliases for the specified identifier.
     *
     * @param string $id
     * @param string[] $aliases
     * @return self
     * @throws InvalidArgumentException If the specified identifier does not exist.
     */
    public function setAliases(string $id, array $aliases): self
    {
        if (!array_key_exists($id, $this->values)) {
            throw new InvalidArgumentException("The value with the specified identifier does not exist: {$id}");
        }

        $object = $this->values[$id];

        foreach ($aliases as $alias) {
            $this->values[$alias] = $object;
        }

        return $this;
    }

    /**
     * Checks if the specified identifier exists.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->values);
    }

    /**
     * Checks if the specified object type exists.
     *
     * @param object $object
     * @return bool
     */
    public function hasTyped(object $object)
    {
        return array_key_exists(get_class($object), $this->values);
    }

    /**
     * Gets all values that depend on the request context.
     *
     * @return array<string,mixed>
     */
    public function values(): array
    {
        /** @var array<string,mixed> */
        $result = [];

        foreach ($this->values as $id => $object) {
            $result[$id] = $object->value();
        }

        return $result;
    }
}
