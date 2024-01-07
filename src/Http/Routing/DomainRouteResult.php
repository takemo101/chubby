<?php

namespace Takemo101\Chubby\Http\Routing;

use RuntimeException;
use InvalidArgumentException;

/**
 * Result of dispatch by DomainRouteDispatcher
 */
class DomainRouteResult
{
    /**
     * constructor
     *
     * @param boolean $found
     * @param DomainRoute|null $route
     * @param array<string,string> $arguments
     * @throws InvalidArgumentException
     */
    public function __construct(
        private bool $found,
        private ?DomainRoute $route = null,
        private array $arguments = []
    ) {
        if ($found && !$route) {
            throw new InvalidArgumentException('route is required');
        }
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
     * Get the found route.
     * If the route is not found, an exception will be thrown.
     *
     * @return DomainRoute
     * @throws RuntimeException
     */
    public function getRoute(): DomainRoute
    {
        if (!$this->route) {
            throw new RuntimeException('route is not found');
        }

        return $this->route;
    }
}
