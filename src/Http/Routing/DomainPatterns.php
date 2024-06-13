<?php

namespace Takemo101\Chubby\Http\Routing;

use InvalidArgumentException;

/**
 * Route patterns for the domain.
 */
class DomainPatterns
{
    /**
     * @var array<string,DomainPattern>
     */
    private array $patterns = [];

    /**
     * constructor
     *
     * @param DomainPattern ...$patterns
     */
    public function __construct(DomainPattern ...$patterns)
    {
        foreach ($patterns as $pattern) {
            $this->add($pattern);
        }
    }

    /**
     * Add a route pattern to the domain.
     *
     * @param DomainPattern $pattern
     * @return self
     * @throws InvalidArgumentException
     */
    public function add(DomainPattern $pattern): self
    {
        $this->patterns[$pattern->pattern] = $pattern;

        return $this;
    }

    /**
     * Check if the route pattern is set for the domain.
     *
     * @param DomainPattern $pattern
     * @return bool
     */
    public function has(DomainPattern $pattern): bool
    {
        return isset($this->patterns[$pattern->pattern]);
    }


    /**
     * Get route patterns.
     *
     * @return DomainPattern[]
     */
    public function patterns(): array
    {
        return array_values($this->patterns);
    }

    /**
     * Create a new instance from the route pattern.
     *
     * @param string ...$patterns
     * @return self
     */
    public static function fromPatterns(string ...$patterns): self
    {
        $objects = array_map(
            fn ($pattern) => new DomainPattern($pattern),
            $patterns
        );

        return new self(
            ...$objects,
        );
    }
}
