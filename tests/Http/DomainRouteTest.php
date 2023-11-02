<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\Http\DomainRouter;
use Takemo101\Chubby\Http\Middleware\DomainRouting;
use Takemo101\Chubby\Http\Routing\DomainRouteCollector;
use Takemo101\Chubby\Http\Routing\DomainRouteContext;
use Takemo101\Chubby\Http\Routing\DomainRouteDispatcher;
use Takemo101\Chubby\Http\Routing\DomainRouteHandler;
use Tests\AppTestCase;

describe(
    'domain route',
    function () {
        test(
            'The domain route configured for the host is found.',
            function (string $route, string $host, array $routeArguments) {
                $dispatcher = DomainRouteDispatcher::fromDomain($route);

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

                $middleware = DomainRouting::fromDomain($route);

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
            'Set a route in RouteCollector and get route information by name.',
            function (
                string $pattern,
                string $name,
            ) {
                $collector = new DomainRouteCollector();

                $collector->addRoute($pattern, fn () => 'test')->setName($name);

                $route = $collector->getNamedRoute($name);

                expect($route)->not->toBeNull($name);
                expect($route->getPattern())->toEqual($pattern);
                expect($route->getHandler()())->toEqual('test');
            }
        )->with([
            ['localhost', 'home'],
            ['{domain}.localhost', 'domain'],
        ]);

        test(
            'Run DomainRouteHandler.',
            function (string $pattern, string $host) {
                /** @var AppTestCase $this */

                $expectMessage = 'test';

                $dispatcher = new DomainRouteDispatcher(
                    new DomainRouteCollector([
                        $pattern => fn () => new class($this->createResponse(), $expectMessage) implements RequestHandlerInterface
                        {
                            public function __construct(
                                public ResponseInterface $response,
                                public string $expectMessage,
                            ) {
                                //
                            }

                            public function handle(ServerRequestInterface $request): ResponseInterface
                            {
                                $this->response->getBody()->write($this->expectMessage);

                                return $this->response;
                            }
                        },
                    ])
                );

                $handler = new DomainRouteHandler(
                    $dispatcher,
                    $this->getContainer(),
                );

                $request = $this->createRequest(
                    uri: 'http://' . $host,
                );

                $response = $handler->handle($request);

                expect($response)->not->toBeNull();
                expect($response->getBody()->__toString())->toEqual($expectMessage);
            }
        )->with([
            ['localhost', 'localhost'],
            ['{domain}.localhost', 'test.localhost'],
        ]);

        test(
            'Configure and run routing on DomainRouter.',
            function (string $pattern, string $host) {
                /** @var AppTestCase $this */

                /** @var DomainRouter */
                $router = $this->getContainer()->get(DomainRouter::class);

                $router->route($pattern, fn () => new class($this->createResponse()) implements RequestHandlerInterface
                {
                    public function __construct(
                        public ResponseInterface $response,
                    ) {
                        //
                    }

                    public function handle(ServerRequestInterface $request): ResponseInterface
                    {
                        $this->response->getBody()->write('test');

                        return $this->response;
                    }
                });

                $response = $router->handle($this->createRequest(
                    uri: 'http://' . $host,
                ));

                expect($response->getStatusCode())->toBe(StatusCodeInterface::STATUS_OK);
                expect($response->getBody()->__toString())->toEqual('test');
            }
        )->with([
            ['localhost.test', 'localhost.test'],
            ['{domain}.localhost.test', 'test.localhost.test'],
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
