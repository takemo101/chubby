<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\Http\Middleware\DomainRouting;
use Takemo101\Chubby\Http\Routing\DomainRouteCollector;
use Takemo101\Chubby\Http\Routing\DomainRouteContext;
use Takemo101\Chubby\Http\Routing\DomainRouteDispatcher;
use Tests\AppTestCase;

describe(
    'domain route',
    function () {
        test(
            'The domain route configured for the host is found.',
            function (string $route, string $host, array $routeArguments) {
                $dispatcher = DomainRouteDispatcher::fromPatterns($route);

                $result = $dispatcher->dispatch($host);

                expect($result->isFound())->toBeTrue();
                expect($result->getArguments())->toEqual($routeArguments);
            },
        )->with('domain-routes');

        test(
            'Execute DomainRoute middleware processes',
            function (string $route, string $host, array $routeArguments) {
                /** @var AppTestCase $this */

                $request = $this->createRequest('GET', 'http://' . $host);
                $response = $this->createResponse();

                $middleware = DomainRouting::pattern($route);

                $mock = new class($response) implements RequestHandlerInterface
                {
                    public function __construct(
                        public ResponseInterface $response,
                        public ?ServerRequestInterface $actualRequest = null,
                    ) {
                        //
                    }

                    public function handle(ServerRequestInterface $request): ResponseInterface
                    {
                        $this->actualRequest = $request;

                        return $this->response;
                    }
                };


                $middleware->process(
                    $request,
                    $mock,
                );

                $context = DomainRouteContext::fromRequest($mock?->actualRequest);

                foreach ($routeArguments as $key => $argument) {
                    expect($context->getArguments()[$key])->toEqual($argument);
                }
            },
        )->with('domain-routes');

        test(
            'Set a route in RouteCollector and check if it exists',
            function (string $pattern) {
                /** @var AppTestCase $this */

                $collector = new DomainRouteCollector();

                $collector->addPattern($pattern);

                expect($collector->hasPattern($pattern))->toBeTrue();
            }
        )->with([
            ['localhost'],
            ['{domain}.localhost'],
        ]);
    }
)->group('domain-route');

dataset(
    'domain-routes',
    [
        ['{domain}.localhost', 'domain.localhost', ['domain' => 'domain']],
        ['example.{domain}.com', 'example.domain.com', ['domain' => 'domain']],
        ['{subdomain}.example.{domain}.com', 'subdomain.example.domain.com', [
            'subdomain' => 'subdomain',
            'domain' => 'domain',
        ]],
    ],
);
