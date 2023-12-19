<?php

namespace Takemo101\Chubby\Container;

use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;

/**
 * A simple container to solve small dependencies.
 */
class InstantContainer implements ContainerInterface
{
    /**
     * @var array<class-string<object>,object>
     */
    private array $dependencies = [];

    /**
     * constructor
     *
     * @param ObjectResolver $resolver
     * @param array<class-string<object>,object> $dependencies
     */
    public function __construct(
        private ObjectResolver $resolver = new ObjectResolver(),
        array $dependencies = [],
    ) {
        foreach ($dependencies as $id => $instance) {
            $this->add($instance, $id);
        }
    }

    /**
     * Add instance.
     *
     * @template T of object
     *
     * @param T $instance
     * @param class-string<T>|null $id
     * @return self
     * @throws LogicException
     */
    public function add(object $instance, ?string $id = null): self
    {
        // null is not allowed as id
        if (is_null($id)) {

            /** @var class-string<T> */
            $id = get_class($instance);
        }
        // if id is not null, check if the instance is an instance of the id
        elseif (!($instance instanceof $id)) {

            $class = get_class($instance);

            throw new LogicException("[{$id}] is not instance of [{$class}]");
        }

        $this->dependencies[$id] = $instance;

        return $this;
    }

    /**
     * Create instance.
     *
     * @template T of object
     *
     * @param class-string<T> $id
     * @return T
     * @throws InvalidArgumentException|ObjectResolverException
     */
    public function create(string $id)
    {
        if (!class_exists($id)) {
            throw new InvalidArgumentException("[{$id}] is not found");
        }

        /** @var T */
        $instance = $this->resolver->resolve($id, $this);

        return $instance;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id)
    {
        if (!isset($this->dependencies[$id])) {
            throw new NotFoundDependencyException("[{$id}] is not found");
        }

        return $this->dependencies[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return isset($this->dependencies[$id]);
    }

    /**
     * Get all dependencies
     *
     * @return array<class-string<object>,object>
     */
    public function dependencies(): array
    {
        return $this->dependencies;
    }
}
