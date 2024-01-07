<?php

namespace Takemo101\Chubby\Http\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class DomainRouteDispatcher
{
    /** @var string */
    public const CommonRequestMethod = 'GET';

    /**
     * constructor
     *
     * @param DomainRouteCollector $routeCollector
     */
    public function __construct(
        private DomainRouteCollector $routeCollector,
    ) {
        //
    }

    /**
     * Dispatch the route configured for the request domain.
     *
     * @param string $domain
     * @return DomainRouteResult
     */
    public function dispatch(string $domain): DomainRouteResult
    {
        $dispatcher = simpleDispatcher(
            function (RouteCollector $r) {
                $routes = $this->routeCollector->getRoutes();

                foreach ($routes as $route) {
                    $r->addRoute(
                        self::CommonRequestMethod,
                        $route->getPattern(),
                        $route,
                    );
                }
            },
        );

        $info = $dispatcher->dispatch(self::CommonRequestMethod, $domain);

        /** @var integer */
        $status = $info[0] ?? Dispatcher::NOT_FOUND;

        /** @var DomainRoute|null */
        $route = $info[1] ?? null;

        /** @var array<string,string> */
        $arguments = $info[2] ?? [];

        return new DomainRouteResult(
            found: $status === Dispatcher::FOUND,
            route: $route,
            arguments: $arguments,
        );
    }

    /**
     * Create a new DomainRouteDispatcher instance from the given domain.
     *
     * @param string $domain
     * @return self
     */
    public static function fromDomain(string $domain): self
    {
        return new self(
            new DomainRouteCollector(
                routes: [
                    $domain => DomainRouteHandleException::createNeverRequestHandler(),
                ],
            ),
        );
    }
}
