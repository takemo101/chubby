<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;
use Takemo101\Chubby\Http\Context;
use Takemo101\Chubby\Http\Middleware\DomainRoute;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\SlimHttpAdapter;

hook()->onByType(
    function (SlimHttpAdapter $http) {

        $http->get(
            '/',
            function (ResponseInterface $response) {
                $response
                    ->getBody()
                    ->write('Hello World!');

                return $response;
            },
        );

        $http->get(
            '/domain',
            function (ResponseInterface $response, string $domain) {
                $response
                    ->getBody()
                    ->write("Hello {$domain}!");

                return $response;
            },
        )->add(DomainRoute::fromDomain('{domain}.localhost'));

        $http->get(
            '/json',
            fn (Context $context) => new JsonRenderer(
                $context->getRequest()->getHeaders(),
            ),
        );

        $http->get(
            '/name/{name}',
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
