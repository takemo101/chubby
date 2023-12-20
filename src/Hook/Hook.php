<?php

namespace Takemo101\Chubby\Hook;

use RuntimeException;
use ReflectionFunction;
use ReflectionNamedType;
use Closure;
use DI\Container;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

class Hook
{
    private ContainerInterface $container;

    /**
     * constructor
     *
     * @param ContainerInterface $container
     * @param array<string,HookFilters> $filters
     */
    public function __construct(
        ContainerInterface $container = new Container(),
        private array $filters = []
    ) {
        $this->container = $container;
    }

    /**
     * Added hook processing.
     *
     * @param string $tag
     * @param string|mixed[]|object $function
     * @param integer $priority
     * @return self
     */
    public function on(
        string $tag,
        string|array|object $function,
        int $priority = HookFilter::DefaultPriority,
    ): self {
        if (isset($this->filters[$tag])) {
            $this->filters[$tag]->add(
                HookFilter::fromCallable(
                    priority: $priority,
                    function: $function,
                ),
            );
        } else {
            $this->filters[$tag] = new HookFilters(
                HookFilter::fromCallable(
                    priority: $priority,
                    function: $function,
                ),
            );
        }

        return $this;
    }

    /**
     * Parse function arguments and add hooks.
     *
     * @param string|mixed[]|object $function
     * @param integer $priority
     * @return self
     * @throws RuntimeException
     */
    public function onByType(
        string|array|object $function,
        int $priority = HookFilter::DefaultPriority,
    ): self {
        if (!is_callable($function)) {
            throw new InvalidArgumentException('The given value is not callable');
        }

        $parameters = (new ReflectionFunction(Closure::fromCallable($function)))
            ->getParameters();

        if (!in_array(count($parameters), [1, 2])) {
            throw new RuntimeException('invalid function parameter');
        }

        $parameter = $parameters[0];

        $type = $parameter->getType();

        if (!($type instanceof ReflectionNamedType)) {
            throw new RuntimeException('invalid function parameter type');
        }

        return $this->on(
            tag: match (true) {
                $type->isBuiltin() => $parameter->getName(),
                default => $type->getName(),
            },
            function: $function,
            priority: $priority,
        );
    }


    /**
     * Remove hook processing.
     * Can be deleted by specifying tag, callable value and priority.
     *
     * @param string $tag
     * @param string|mixed[]|object $function
     * @param integer $priority
     * @return self
     */
    public function remove(
        string $tag,
        string|array|object $function,
        int $priority = HookFilter::DefaultPriority,
    ): self {
        if (isset($this->filters[$tag])) {

            $filters = $this->filters[$tag];

            $filters->remove(
                priority: $priority,
                function: $function,
            );

            if ($filters->isEmpty()) {
                unset($this->filters[$tag]);
            }
        }

        return $this;
    }

    /**
     * Remove all hook processing for tags
     *
     * @param string $tag
     * @return self
     */
    public function removeAllByTag(string $tag): self
    {
        if (isset($this->filters[$tag])) {
            unset($this->filters[$tag]);
        }

        return $this;
    }

    /**
     * Is the tag registered?
     *
     * @param string $tag
     *
     * @return boolean
     */
    public function hasTag(string $tag): bool
    {
        return isset($this->filters[$tag]);
    }

    /**
     * Execute hook processing on tags.
     * This process does not cycle the return value through hook processes.
     *
     * @param string $tag
     * @param mixed $parameter
     * @return mixed
     */
    public function do(string $tag, $parameter): mixed
    {
        if (!isset($this->filters[$tag])) {
            return $parameter;
        }

        $result = $parameter;

        $filters = $this->filters[$tag];

        foreach ($filters->all() as $filter) {
            foreach ($filter->actions() as $action) {

                $result = call_user_func_array(
                    $action->getCallable(),
                    // Pass initial parameters if filter output is null
                    [$result ?? $parameter, $this->container],
                );
            }
        }

        return $result ?? $parameter;
    }

    /**
     * Execute hook processing on tags.
     * This process does not cycle the return value through hook processes.
     * Get the tag from the object type.
     *
     * @param object $object
     * @return mixed
     */
    public function doByType(object $object): mixed
    {
        $type = get_class($object);

        return $this->do($type, $object);
    }
}
