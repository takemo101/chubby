<?php

namespace Takemo101\Chubby\Http\Routing;

use Closure;

/**
 * Result of dispatch by DomainRouteDispatcher
 */
class DomainRouteResult
{
    /**
     * @var Closure
     */
    private Closure $handler;

    /**
     * constructor
     *
     * @param boolean $found
     * @param callable $handler
     * @param array<string,string> $arguments
     */
    public function __construct(
        private bool $found,
        callable $handler,
        private array $arguments = []
    ) {
        $this->handler = $handler instanceof Closure
            ? $handler
            : Closure::fromCallable($handler);
    }

    /**
     * Obtain whether the domain route was found using a boolean value.
     *
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->found;
    }

    /**
     * Get the arguments of the found route.
     *
     * @return array<string,string>
     */
    public function getArguments(bool $urlDecode = true): array
    {
        if (!$urlDecode) {
            return $this->arguments;
        }

        return array_map('rawurldecode', $this->arguments);
    }

    /**
     * Get the handler of the found route.
     *
     * @return Closure
     */
    public function getHandler(): Closure
    {
        return $this->handler;
    }
}
