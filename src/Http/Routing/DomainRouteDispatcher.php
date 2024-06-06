<?php

namespace Takemo101\Chubby\Http\Routing;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

/**
 * Dispatch the route configured for the request domain.
 */
class DomainRouteDispatcher
{
    /** @var string */
    public const CommonRequestMethod = 'GET';

    /**
     * constructor
     *
     * @param DomainRoutePatterns $patterns
     */
    public function __construct(
        private DomainRoutePatterns $patterns,
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
                $patterns = $this->patterns->patterns();

                foreach ($patterns as $pattern) {
                    $r->addRoute(
                        self::CommonRequestMethod,
                        $pattern,
                        null,
                    );
                }
            },
        );

        $info = $dispatcher->dispatch(self::CommonRequestMethod, $domain);

        /** @var integer */
        $status = $info[0] ?? Dispatcher::NOT_FOUND;

        /** @var array<string,string> */
        $arguments = $info[2] ?? [];

        return new DomainRouteResult(
            found: $status === Dispatcher::FOUND,
            arguments: $arguments,
        );
    }

    /**
     * Create a new instance from the route pattern.
     *
     * @param string ...$patterns
     * @return self
     */
    public static function fromPatterns(string ...$patterns): self
    {
        return new self(
            new DomainRoutePatterns(
                ...$patterns,
            ),
        );
    }
}
