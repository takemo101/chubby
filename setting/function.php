<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;
use Takemo101\Chubby\Http\Context;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\SlimHttpAdapter;

hook()->onByType(
    function (SlimHttpAdapter $slim) {
        $slim->get(
            '/',
            function (ResponseInterface $response) {
                $response
                    ->getBody()
                    ->write('Hello World!');

                return $response;
            },
        );

        $slim->get(
            '/json',
            function (Context $context) {

                return new JsonRenderer(
                    $context->request->getHeaders(),
                );
            },
        );

        $slim->get(
            '/{name}',
            function (ResponseInterface $response, string $name) {
                $response
                    ->getBody()
                    ->write($name);

                return $response;
            },
        );

        $slim->group(
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
