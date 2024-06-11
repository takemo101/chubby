<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\Http\Middleware\DomainRouting;
use Takemo101\Chubby\Http\Routing\DomainPattern;
use Takemo101\Chubby\Http\Routing\DomainPatterns;
use Takemo101\Chubby\Http\Routing\DomainRouteContext;
use Takemo101\Chubby\Http\Routing\DomainRouteDispatcher;
use Tests\AppTestCase;

describe(
    'domain route',
    function () {

        test(
            'The domain pattern is created.',
            function (string $pattern) {
                $domainPattern = new DomainPattern($pattern);

                expect($domainPattern->pattern)->toEqual($pattern);
            },
        )->with([
            ['localhost'],
            ['{domain}.localhost'],
        ]);

        test(
            'The domain pattern is replaced to a pattern for FastRoute.',
            function (string $pattern, string $expected) {
                $domainPattern = new DomainPattern($pattern);

                expect($domainPattern->replaceDotsToSlashes())->toEqual($expected);
            },
        )->with([
            ['localhost', 'localhost'],
            ['{domain}.localhost', '{domain}/localhost'],
            ['{subdomain}.example.{domain}.com', '{subdomain}/example/{domain}/com'],
            ['{domain:.+}.localhost', '{domain:.+}/localhost'],
        ]);

        test(
            'The domain route configured for the host is found.',
            function (string $route, string $host, array $routeArguments) {
                $dispatcher = DomainRouteDispatcher::fromPatterns($route);

                $result = $dispatcher->dispatch($host);

                expect($result->isFound())->toBeTrue();
                expect($result->getArguments()->getArguments())->toEqual($routeArguments);
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
                    expect($context->getArguments()->getArgument($key))->toEqual($argument);
                }
            },
        )->with('domain-routes');

        test(
            'Set a route in RouteCollector and check if it exists',
            function (string $pattern) {
                /** @var AppTestCase $this */

                $collector = new DomainPatterns();

                $domainPattern = new DomainPattern($pattern);

                $collector->add($domainPattern);

                expect($collector->has($domainPattern))->toBeTrue();
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
