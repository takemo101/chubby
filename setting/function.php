<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Http\Context;
use Takemo101\Chubby\Http\DomainRouter;
use Takemo101\Chubby\Http\Factory\SlimFactory;
use Takemo101\Chubby\Http\Middleware\DomainRouting;
use Takemo101\Chubby\Http\Renderer\HtmlRenderer;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\Renderer\StaticRenderer;
use Takemo101\Chubby\Http\SlimHttp;
use Takemo101\Chubby\Support\ApplicationPath;

hook()
    ->onTyped(
        function (SlimHttp $http) {

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
                '/static/{path:.*}',
                function (
                    ServerRequestInterface $request,
                    ApplicationPath $appPath,
                    LocalFilesystem $filesystem,
                    string $path,
                ) {

                    $filePath = $appPath->getBasePath(
                        'public',
                        'assets',
                        $path,
                    );

                    if (!$filesystem->exists($filePath)) {
                        throw new HttpNotFoundException(
                            $request,
                            sprintf('File not found: %s', $filePath),
                        );
                    }

                    return StaticRenderer::fromPath(
                        $filePath,
                    );
                },
            )->setName('static');

            $http->get(
                '/image',
                function () {
                    return new HtmlRenderer(
                        <<<HTML
                            <img src="/static/sample.jpeg" />
                        HTML
                    );
                },
            )->setName('image');

            $http->get(
                '/script',
                function () {
                    return new HtmlRenderer(
                        <<<HTML
                            <script src="/static/sample.js"></script>
                            <link rel="stylesheet" href="/static/sample.css" />
                        HTML
                    );
                },
            )->setName('script');

            $http->get(
                '/domain',
                function (ResponseInterface $response, string $domain) {
                    $response
                        ->getBody()
                        ->write("Hello {$domain}!");

                    return $response;
                },
            )->add(DomainRouting::fromDomain('{domain}.localhost'));

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
                function (RouteCollectorProxyInterface $proxy) {
                    $proxy->get('/a', function (
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
    )
    ->onTyped(
        function (DomainRouter $router, ContainerInterface $container) {

            /** @var SlimFactory */
            $factory = $container->get(SlimFactory::class);

            /** @var SlimHttp */
            $http = $container->get(SlimHttp::class);

            $slim = $factory->create();

            $slim->get(
                '/',
                function (ResponseInterface $response, string $domain) {
                    $response
                        ->getBody()
                        ->write("Hello {$domain}!");

                    return $response;
                },
            );

            $router->mount('localhost', $http)->setName('base');
            $router->mount('{domain}.localhost', $slim)->setName('domain');
        },
    );
