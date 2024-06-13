<?php

namespace Takemo101\Chubby\Http\Routing;

/**
 * Result of dispatch by DomainRouteDispatcher
 */
readonly class DomainRouteResult
{
    /**
     * constructor
     *
     * @param boolean $found
     * @param array<string,string> $arguments
     */
    public function __construct(
        private bool $found,
        private array $arguments = []
    ) {
        //
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
     * @return RouteArguments
     */
    public function getArguments(bool $urlDecode = true): RouteArguments
    {
        return RouteArguments::create(
            arguments: $this->arguments,
            urlDecode: $urlDecode,
        );
    }
}
