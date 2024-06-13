<?php

namespace Takemo101\Chubby\Hook;

use ReflectionFunction;
use ReflectionNamedType;
use Closure;
use DI\Container;
use Psr\Container\ContainerInterface;

class Hook
{
    private ContainerInterface $container;

    /**
     * Delayed processing parameters.
     * This parameter is used when delayed processing hooks are added.
     *
     * @var array<string,mixed>
     */
    private array $delayedParameters = [];

    /**
     * constructor
     *
     * @param ContainerInterface $container
     * @param array<string,HookActions> $actions
     */
    public function __construct(
        ContainerInterface $container = new Container(),
        private array $actions = []
    ) {
        $this->container = $container;
    }

    /**
     * Added hook processing.
     *
     * @param string $tag
     * @param callable $function
     * @param integer $priority
     * @return self
     */
    public function on(
        string $tag,
        callable $function,
        int $priority = HookAction::DefaultPriority,
    ): self {
        if (isset($this->actions[$tag])) {
            $this->actions[$tag]->add(
                HookAction::fromCallable(
                    priority: $priority,
                    function: $function,
                ),
            );
        } else {
            $this->actions[$tag] = new HookActions(
                HookAction::fromCallable(
                    priority: $priority,
                    function: $function,
                ),
            );
        }

        if (isset($this->delayedParameters[$tag])) {
            $this->do($tag, $this->delayedParameters[$tag]);
        }

        return $this;
    }

    /**
     * Parse function arguments and add hooks.
     *
     * @param callable $function
     * @param integer $priority
     * @return self
     * @throws HookException
     */
    public function onTyped(
        callable $function,
        int $priority = HookAction::DefaultPriority,
    ): self {
        $callback = Closure::fromCallable($function);

        $parameters = (new ReflectionFunction($callback))
            ->getParameters();

        if (!in_array(count($parameters), [1, 2])) {
            throw HookException::invalidFunctionParameterCount();
        }

        $parameter = $parameters[0];

        $type = $parameter->getType();

        if (!($type instanceof ReflectionNamedType)) {
            throw HookException::invalidFunctionParameterType();
        }

        return $this->on(
            tag: match (true) {
                $type->isBuiltin() => $parameter->getName(),
                default => $type->getName(),
            },
            function: $callback,
            priority: $priority,
        );
    }

    /**
     * Remove all hook processing for tags
     *
     * @param string $tag
     * @return self
     */
    public function remove(string $tag): self
    {
        if (isset($this->actions[$tag])) {
            unset($this->actions[$tag]);
        }

        if (isset($this->delayedParameters[$tag])) {
            unset($this->delayedParameters[$tag]);
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
        return isset($this->actions[$tag]);
    }

    /**
     * Execute hook processing on tags.
     * This process does not cycle the return value through hook processes.
     *
     * @param string $tag
     * @param mixed $parameter
     * @param boolean $delayed Delayed processing
     * @return mixed
     */
    public function do(string $tag, mixed $parameter, bool $delayed = false): mixed
    {
        if ($delayed) {
            $this->delayedParameters[$tag] = $parameter;
        }

        if (!isset($this->actions[$tag])) {
            return $parameter;
        }

        $result = $parameter;

        $actions = $this->actions[$tag];

        foreach ($actions->all() as $filter) {
            foreach ($filter->getCallbacks() as $callback) {

                $result = call_user_func_array(
                    $callback,
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
     * @param boolean $delayed Delayed processing
     * @return mixed
     */
    public function doTyped(object $object, bool $delayed = false): mixed
    {
        $type = get_class($object);

        return $this->do($type, $object, $delayed);
    }
}
