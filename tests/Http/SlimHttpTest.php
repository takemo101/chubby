<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\SlimHttp;
use Tests\AppTestCase;

describe(
    'http',
    function () {
        test(
            'Create an instance of SlimHttp to handle requests',
            function () {
                /** @var AppTestCase $this */

                /** @var App */
                $app = $this->getContainer()->get(App::class);
                $configurer = $this->getContainer()->get(SlimConfigurer::class);
                $dispatcher = $this->getContainer()->get(EventDispatcherInterface::class);

                $http = new SlimHttp($app, $configurer, $dispatcher);

                $request = $this->createRequest('GET', '/');

                $response = $http->handle($request);

                expect($response->getStatusCode())->toEqual(
                    StatusCodeInterface::STATUS_NOT_FOUND,
                );
            },
        );

        test(
            'Handle requests with SlimHttp',
            function (string $method) {
                /** @var AppTestCase $this */

                $uri = '/test';
                $body = 'test';
                $statusCode = StatusCodeInterface::STATUS_ACCEPTED;

                $this->getHttp()->{$method}(
                    $uri,
                    function (
                        ResponseInterface $response,
                    ) use ($body, $statusCode) {
                        $response->getBody()->write($body);

                        return $response->withStatus($statusCode);
                    },
                );

                $actual = $this->{$method}($uri);

                expect($actual->getStatusCode())->toEqual($statusCode);
                expect($actual->getBody()->__toString())->toEqual($body);
            },
        )->with([
            'get',
            'options',
        ]);

        test(
            'Handle form requests with SlimHttp',
            function (string $method) {
                /** @var AppTestCase $this */

                $uri = '/form-test';
                $data = ['message' => 'test'];
                $statusCode = StatusCodeInterface::STATUS_ALREADY_REPORTED;

                $this->getHttp()->{$method}(
                    $uri,
                    function (
                        ServerRequestInterface $request,
                        ResponseInterface $response,
                    ) use ($statusCode) {

                        $body = $request->getParsedBody();

                        $response->getBody()->write(
                            $body['message'],
                        );

                        return $response->withStatus($statusCode);
                    },
                );

                $actual = $this->{$method}($uri, $data);

                expect($actual->getStatusCode())->toEqual($statusCode);
                expect($actual->getBody()->__toString())->toEqual($data['message']);
            },
        )->with([
            'post',
            'put',
            'patch',
            'delete',
        ]);

        test(
            'Handle json requests with SlimHttp',
            function (string $method) {
                /** @var AppTestCase $this */

                $uri = '/json-test';
                $data = ['message' => 'test'];
                $statusCode = StatusCodeInterface::STATUS_ALREADY_REPORTED;

                $this->getHttp()->{$method}(
                    $uri,
                    function (
                        ServerRequestInterface $request,
                    ) use ($statusCode) {

                        $body = $request->getParsedBody();

                        return new JsonRenderer($body, $statusCode);
                    },
                );

                $actual = $this->{$method . 'Json'}($uri, $data);

                expect($actual->getStatusCode())->toEqual($statusCode);
                expect($actual->getHeaderLine('Content-Type'))->toContain('application/json');
                expect($actual->getBody()->__toString())->toBeJson();
                expect($actual->getBody()->__toString())->toEqual(json_encode($data));
            },
        )->with([
            'post',
            'put',
            'patch',
            'delete',
        ]);
    }
)->group('http');
