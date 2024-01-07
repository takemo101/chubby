<?php

namespace Takemo101\Chubby\Http\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\Http\Middleware\DomainRouting;

class DomainRouteHandler implements RequestHandlerInterface
{
    /**
     * constructor
     *
     * @param DomainRouteDispatcher $dispatcher
     */
    public function __construct(
        private DomainRouteDispatcher $dispatcher,
    ) {
        //
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = new DomainRouting($this->dispatcher);

        $results = $route->performRouting($request);

        /** @var ServerRequestInterface */
        $routedRequest = $results[0];

        /** @var DomainRouteResult */
        $routedResult = $results[1];

        $route = $routedResult->getRoute();

        return $route->getRequestHandler()->handle(
            $routedRequest
        );
    }
}
