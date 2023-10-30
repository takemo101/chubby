<?php

namespace Takemo101\Chubby\Http\Support;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

final readonly class DomainRouteDispatcher
{
    /** @var string */
    public const CommonRequestMethod = 'GET';

    /**
     * @var string[]
     */
    private array $routes;

    /**
     * constructor
     *
     * @param string ...$routes
     */
    public function __construct(
        string ...$routes,
    ) {
        $this->routes = array_unique($routes);
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
                foreach ($this->routes as $route) {
                    $r->addRoute(self::CommonRequestMethod, $route, 'empty');
                }
            },
        );

        $info = $dispatcher->dispatch(self::CommonRequestMethod, $domain);

        /** @var integer */
        $routeStatus = $info[0] ?? Dispatcher::NOT_FOUND;

        /** @var array<string,string> */
        $routeArguments = $info[2] ?? [];

        return new DomainRouteResult(
            $routeStatus === Dispatcher::FOUND,
            $routeArguments,
        );
    }
}
