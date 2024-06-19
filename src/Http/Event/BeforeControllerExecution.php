<?php

namespace Takemo101\Chubby\Http\Event;

use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Event\StoppableEvent;
use Takemo101\Chubby\Http\Routing\RouteArguments;

/**
 * This is an event before running the controller.
 */
class BeforeControllerExecution extends StoppableEvent
{
    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     * @param RouteArguments $routeArguments
     */
    public function __construct(
        private ServerRequestInterface $request,
        private RouteArguments $routeArguments,
    ) {
        //
    }

    /**
     * Get the request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Get the route arguments.
     *
     * @return RouteArguments
     */
    public function getRouteArguments(): RouteArguments
    {
        return $this->routeArguments;
    }
}
