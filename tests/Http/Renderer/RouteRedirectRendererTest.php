<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Http\Renderer\RouteRedirectRenderer;
use Mockery as m;

it(
    'can render a route redirect',
    function () {
        $route = 'home';
        $data = ['id' => 1];
        $query = ['sort' => 'asc'];
        $status = StatusCodeInterface::STATUS_FOUND;
        $headers = [];

        $request = m::mock(ServerRequestInterface::class);
        $response = m::mock(ResponseInterface::class);
        $routeParser = m::mock(RouteParserInterface::class);

        $container = m::mock(ApplicationContainer::class);
        $container->shouldReceive('get')->with(RouteParserInterface::class)->andReturn($routeParser);

        $routeParser->shouldReceive('urlFor')->with($route, $data, $query)->andReturn('http://example.com');

        $response->shouldReceive('withHeader')
            ->with('Location', 'http://example.com')
            ->andReturnSelf();
        $response->shouldReceive('withStatus')
            ->with($status)
            ->andReturnSelf();

        $renderer = new RouteRedirectRenderer($route, $data, $query, $status, $headers);
        $renderer->setContainer($container);

        $result = $renderer->render($request, $response);

        expect($result)->toBe($response);
    }
)->group('route-redirect-renderer', 'renderer');
