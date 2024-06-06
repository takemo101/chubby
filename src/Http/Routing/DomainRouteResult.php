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
     * @return array<string,string>
     */
    public function getArguments(bool $urlDecode = true): array
    {
        if (!$urlDecode) {
            return $this->arguments;
        }

        return array_map('rawurldecode', $this->arguments);
    }
}
