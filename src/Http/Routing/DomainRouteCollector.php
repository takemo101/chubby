<?php

namespace Takemo101\Chubby\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DomainRouteCollector
{
    /**
     * @var array<string,DomainRoute>
     */
    private array $routes = [];

    /**
     * constructor
     *
     * @param array<string,callable> $routes
     */
    public function __construct(
        array $routes = [],
    ) {
        foreach ($routes as $domain => $handler) {
            $this->addRoute($domain, $handler);
        }
    }

    /**
     * Add a route
     *
     * @param string $pattern
     * @param callable(ServerRequestInterface):RequestHandlerInterface $handler
     * @return DomainRoute
     */
    public function addRoute(string $pattern, callable $handler): DomainRoute
    {
        $route = new DomainRoute($pattern, $handler);

        $this->routes[$pattern] = $route;

        return $route;
    }

    /**
     * Get route by name
     *
     * @param string $name
     * @return DomainRoute|null
     */
    public function getNamedRoute(string $name): ?DomainRoute
    {
        foreach ($this->routes as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Get routes
     *
     * @return array<string,DomainRoute>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
