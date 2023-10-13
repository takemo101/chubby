<?php

namespace Takemo101\Chubby\Hook;

use RuntimeException;
use ReflectionFunction;
use ReflectionNamedType;
use Closure;

final class Hook
{
    /**
     * constructor
     *
     * @param array<string,HookFilters> $filters
     */
    public function __construct(
        private array $filters = []
    ) {
        //
    }

    /**
     * Added hook processing.
     *
     * @param string $tag
     * @param object|mixed[]|string $function
     * @param integer $priority
     * @return self
     */
    public function on(
        string $tag,
        object|array|string $function,
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
     * @param Closure $function
     * @param integer $priority
     * @return self
     * @throws RuntimeException
     */
    public function onByType(
        Closure $function,
        int $priority = HookFilter::DefaultPriority,
    ): self {
        $parameters = (new ReflectionFunction($function))
            ->getParameters();

        if (count($parameters) !== 1) {
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
     * Removal of hook processing.
     *
     * @param string $tag
     * @param object|mixed[]|string $function
     * @param integer $priority
     * @return self
     */
    public function remove(
        string $tag,
        object|array|string $function,
        int $priority = HookFilter::DefaultPriority,
    ): self {
        if (isset($this->filters[$tag])) {
            $this->filters[$tag]->remove(
                priority: $priority,
                function: $function,
            );
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
     *
     * @param string $tag
     * @param mixed $parameter
     * @return mixed
     */
    public function apply(string $tag, $parameter): mixed
    {
        if (!isset($this->filters[$tag])) {
            return $parameter;
        }

        $result = $parameter;

        $filters = $this->filters[$tag];

        foreach ($filters->all() as $filter) {
            foreach ($filter->actions() as $action) {

                $result = call_user_func_array(
                    $action->function,
                    // Pass initial parameters if filter output is null
                    [$result ?? $parameter],
                );
            }
        }

        return $result;
    }

    /**
     * Execute hook processing on tags.
     * Get the tag from the object.
     *
     * @param object $object
     * @return mixed
     */
    public function applyByObject(object $object): mixed
    {
        $type = get_class($object);

        return $this->apply($type, $object);
    }

    /**
     * Execute hook processing on tags.
     * This process does not cycle the return value through hook processes.
     *
     * @param string $tag
     * @param mixed $parameter
     * @return void
     */
    public function doAction(string $tag, $parameter): void
    {
        if (!isset($this->filters[$tag])) {
            return;
        }

        $filters = $this->filters[$tag];

        foreach ($filters->all() as $filter) {
            foreach ($filter->actions() as $action) {

                call_user_func_array(
                    $action->function,
                    [$parameter],
                );
            }
        }
    }

    /**
     * Execute hook processing on tags.
     * This process does not cycle the return value through hook processes.
     * Get the tag from the object.
     *
     * @param object $object
     * @return void
     */
    public function doActionByObject(object $object): void
    {
        $type = get_class($object);

        $this->doAction($type, $object);
    }
}
