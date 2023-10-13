<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Http\Message\ResponseInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App as Slim;
use Slim\Middleware\ErrorMiddleware;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;

hook()->onByType(
    function (Slim $slim) {
        $slim->addBodyParsingMiddleware();
        $slim->addRoutingMiddleware();
        $slim->add(BasePathMiddleware::class);
        $slim->add(ErrorMiddleware::class);

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
            '/{name}',
            function (ResponseInterface $response, string $name) {
                $response
                    ->getBody()
                    ->write($name);

                return $response;
            },
        );
    },
);
