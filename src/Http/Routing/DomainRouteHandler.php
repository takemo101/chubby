<?php

namespace Takemo101\Chubby\Http\Routing;

use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\Http\Middleware\DomainRouting;

final readonly class DomainRouteHandler implements RequestHandlerInterface
{
    /**
     * constructor
     *
     * @param DomainRouteDispatcher $dispatcher
     * @param InvokerInterface $invoker
     */
    public function __construct(
        private DomainRouteDispatcher $dispatcher,
        private InvokerInterface $invoker,
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

        $handler = $this->invokeRoute(
            $routedResult,
            $routedRequest,
        );

        return $handler->handle($routedRequest);
    }

    /**
     * Invoke routed result.
     *
     * @param DomainRouteResult $routedResult
     * @param ServerRequestInterface $routedRequest
     * @return RequestHandlerInterface
     */
    private function invokeRoute(
        DomainRouteResult $routedResult,
        ServerRequestInterface $routedRequest,
    ): RequestHandlerInterface {
        /** @var RequestHandlerInterface */
        $handler = $this->invoker->call(
            $routedResult->getHandler(),
            [
                'request' => $routedRequest,
            ],
        );

        return $handler;
    }
}
