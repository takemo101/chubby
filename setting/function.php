<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Http\Message\ResponseInterface;
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
