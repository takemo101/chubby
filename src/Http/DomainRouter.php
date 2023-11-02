<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\ResponseEmitter;
use Takemo101\Chubby\Http\Routing\DomainRoute;
use Takemo101\Chubby\Http\Routing\DomainRouteCollector;

final readonly class DomainRouter
{
    /**
     * constructor
     *
     * @param MiddlewareDispatcherInterface $dispatcher
     * @param DomainRouteCollector $routeCollector
     */
    public function __construct(
        private MiddlewareDispatcherInterface $dispatcher,
        private DomainRouteCollector $routeCollector,
    ) {
        //
    }

    /**
     * Add a new middleware to the stack
     *
     * @param MiddlewareInterface|string|callable $middleware
     * @return self
     */
    public function add($middleware): self
    {
        $this->dispatcher->add(
            $middleware
        );

        return $this;
    }

    /**
     * Add a route
     *
     * @param string $pattern
     * @param callable $handler
     * @return DomainRoute
     */
    public function route(string $pattern, callable $handler): DomainRoute
    {
        return $this->routeCollector->addRoute($pattern, $handler);
    }

    /**
     * Run slim application
     *
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function run(?ServerRequestInterface $request = null): void
    {
        if (!$request) {
            $request = ServerRequestCreatorFactory::create()
                ->createServerRequestFromGlobals();
        }

        $response = $this->handle($request);

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

    /**
     * Handle a request
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dispatcher->handle($request);
    }
}
