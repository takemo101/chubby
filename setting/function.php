<?php

// Executed after DI container dependency settings are completed.
// Here, mainly configure routing and middleware.

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteCollectorProxy;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;
use Takemo101\Chubby\Http\Context;
use Takemo101\Chubby\Http\DomainRouter;
use Takemo101\Chubby\Http\Factory\SlimFactory;
use Takemo101\Chubby\Http\Middleware\DomainRouting;
use Takemo101\Chubby\Http\Renderer\HtmlRenderer;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\Renderer\StaticRenderer;
use Takemo101\Chubby\Http\SlimHttpAdapter;
use Takemo101\Chubby\Support\ApplicationPath;

hook()
    ->onByType(
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
    )
    ->onByType(
        function (DomainRouter $router) {
            $router->route(
                'localhost',
                fn (
                    SlimHttpAdapter $http,
                ) => $http,
            )->setName('base');

            $router->route(
                '{domain}.localhost',
                function (
                    SlimFactory $factory,
                    SlimConfigurer $configurer,
                ) {
                    $slim = $factory->create();

                    $slim->get(
                        '/',
                        function (
                            ResponseInterface $response,
                            string $domain,
                        ) {
                            $response
                                ->getBody()
                                ->write("Hello {$domain}!");

                            return $response;
                        },
                    );

                    return $configurer->configure($slim);
                }
            )->setName('domain');
        },
    );
