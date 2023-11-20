<?php

use Takemo101\Chubby\Http\ErrorHandler\ErrorSetting;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\ErrorHandler\JsonErrorResponseRender;
use Tests\AppTestCase;

describe(
    'JsonErrorResponseRender',
    function () {

        it(
            'should create and return a response renderer with JSON content',
            function (string $contentType) {
                /** @var AppTestCase $this */

                // Create a mock for the ServerRequestInterface
                $request = Mockery::mock(ServerRequestInterface::class);
                $request->shouldReceive('getHeaderLine')
                    ->with('Accept')
                    ->andReturn($contentType);

                $response = $this->createResponse();

                // Create a mock for the Throwable
                $exception = Mockery::mock(Exception::class);

                $setting = new ErrorSetting(
                    displayErrorDetails: true,
                );

                // Create an instance of the JsonErrorResponseRender class
                $errorRender = new JsonErrorResponseRender();

                $actual = $errorRender->render($request, $response, $exception, $setting);

                expect($actual->getHeaderLine('Content-Type'))->toBe('application/json');
            }
        )->with([
            'text/html',
            'application/json',
        ]);
    }
)->group('json-error-response-render', 'error-handler');
