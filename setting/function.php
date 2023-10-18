<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;
use Takemo101\Chubby\Http\Context;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\SlimHttpAdapter;
use Takemo101\Chubby\Support\Environment;

hook()->onByType(
    function (SlimHttpAdapter $http) {

        $http->get(
            '/',
            function (ResponseInterface $response, Environment $env) {
                $response
                    ->getBody()
                    ->write('Hello World!');

                return $response;
            },
        );

        $http->get(
            '/json',
            fn (Context $context) => new JsonRenderer(
                $context->request->getHeaders(),
            ),
        );

        $http->get(
            '/{name}',
            function (ResponseInterface $response, string $name) {
                $response
                    ->getBody()
                    ->write($name);

                return $response;
            },
        );

        $http->group(
            '/group',
            function (RouteCollectorProxy $group) {
                $group->get('/a', function (
                    ResponseInterface $response,
                ) {
                    $response
                        ->getBody()
                        ->write('This route is route.a');

                    return $response;
                })->setName('route.a');
            },
        );
    },
);
