<?php

namespace Takemo101\Chubby\Hook;

/**
 * collection of filters
 */
class HookFilters
{
    /**
     * @var array<integer,HookFilter>
     */
    private array $filters = [];

    /**
     * constructor
     *
     * @param HookFilter ...$filters
     */
    public function __construct(
        HookFilter ...$filters,
    ) {
        $this->add(...$filters);
    }

    /**
     * Add filter
     *
     * @param HookFilter ...$filters
     * @return self
     */
    public function add(HookFilter ...$filters): self
    {
        foreach ($filters as $filter) {
            $priority = $filter->adjustPriority($this->filters);

            $this->filters[$priority] = $filter;
        }

        ksort($this->filters);

        return $this;
    }

    /**
     * Check if there are any filters.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->filters);
    }

    /**
     * Get filter by priority.
     *
     * @return HookFilter|null
     */
    public function get(int $priority): ?HookFilter
    {
        return $this->filters[$priority] ?? null;
    }

    /**
     * Get all filters.
     *
     * @return array<integer,HookFilter>
     */
    public function all(): array
    {
        return $this->filters;
    }

    /**
     * Clear all filters.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->filters = [];

        return $this;
    }
}
