<?php

namespace Takemo101\Chubby\Http\Routing;

use InvalidArgumentException;

/**
 * Route patterns for the domain.
 */
class DomainRoutePatterns
{
    /**
     * @var string[]
     */
    private array $patterns = [];

    /**
     * constructor
     *
     * @param string ...$patterns
     */
    public function __construct(string ...$patterns)
    {
        foreach ($patterns as $pattern) {
            $this->add($pattern);
        }
    }

    /**
     * Add a route pattern to the domain.
     *
     * @param string $pattern
     * @return self
     * @throws InvalidArgumentException
     */
    public function add(string $pattern): self
    {
        if (empty($pattern)) {
            throw new InvalidArgumentException('The pattern is empty.');
        }

        $patterns = [
            ...$this->patterns,
            $pattern,
        ];

        $this->patterns = array_unique($patterns);

        return $this;
    }

    /**
     * Check if the route pattern is set for the domain.
     *
     * @param string $pattern
     * @return bool
     */
    public function has(string $pattern): bool
    {
        return in_array($pattern, $this->patterns, true);
    }


    /**
     * Get route patterns.
     *
     * @return string[]
     */
    public function patterns(): array
    {
        return $this->patterns;
    }
}
