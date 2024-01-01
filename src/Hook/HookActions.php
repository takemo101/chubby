<?php

namespace Takemo101\Chubby\Hook;

/**
 * collection of actions
 */
class HookActions
{
    /**
     * @var array<integer,HookAction>
     */
    private array $actions = [];

    /**
     * constructor
     *
     * @param HookAction ...$actions
     */
    public function __construct(
        HookAction ...$actions,
    ) {
        $this->add(...$actions);
    }

    /**
     * Add filter
     *
     * @param HookAction ...$actions
     * @return self
     */
    public function add(HookAction ...$actions): self
    {
        foreach ($actions as $filter) {
            $priority = $filter->adjustPriority($this->actions);

            $this->actions[$priority] = $filter;
        }

        ksort($this->actions);

        return $this;
    }

    /**
     * Check if there are any actions.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->actions);
    }

    /**
     * Get filter by priority.
     *
     * @return HookAction|null
     */
    public function get(int $priority): ?HookAction
    {
        return $this->actions[$priority] ?? null;
    }

    /**
     * Get all actions.
     *
     * @return array<integer,HookAction>
     */
    public function all(): array
    {
        return $this->actions;
    }

    /**
     * Clear all actions.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->actions = [];

        return $this;
    }
}
