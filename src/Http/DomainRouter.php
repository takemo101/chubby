<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\ResponseEmitter;
use Takemo101\Chubby\Http\Routing\DomainRoute;
use Takemo101\Chubby\Http\Routing\DomainRouteCollector;

class DomainRouter implements RequestHandlerInterface
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
     * Add a route from a request handler
     *
     * @param string $pattern
     * @param RequestHandlerInterface $handler
     * @return DomainRoute
     */
    public function mount(string $pattern, RequestHandlerInterface $handler): DomainRoute
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
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dispatcher->handle($request);
    }
}
