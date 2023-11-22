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
                        $route->getHandler(),
                    );
                }
            },
        );

        $info = $dispatcher->dispatch(self::CommonRequestMethod, $domain);

        /** @var integer */
        $routeStatus = $info[0] ?? Dispatcher::NOT_FOUND;

        /** @var callable */
        $routeHandler = $info[1] ?? DomainRouteHandleException::createThrowHandler();

        /** @var array<string,string> */
        $routeArguments = $info[2] ?? [];

        return new DomainRouteResult(
            found: $routeStatus === Dispatcher::FOUND,
            handler: $routeHandler,
            arguments: $routeArguments,
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
                    $domain => fn () => DomainRouteHandleException::createThrowHandler(),
                ],
            ),
        );
    }
}
