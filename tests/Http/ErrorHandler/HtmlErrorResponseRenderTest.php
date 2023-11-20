<?php

use Takemo101\Chubby\Http\ErrorHandler\HtmlErrorResponseRender;
use Takemo101\Chubby\Http\ErrorHandler\ErrorSetting;
use Psr\Http\Message\ServerRequestInterface;
use Tests\AppTestCase;

describe(
    'HtmlErrorResponseRender',
    function () {

        it(
            'should create and return a response renderer with HTML content',
            function () {
                /** @var AppTestCase $this */

                // Create a mock for the ServerRequestInterface
                $request = Mockery::mock(ServerRequestInterface::class);
                $request->shouldReceive('getHeaderLine')
                    ->with('Accept')
                    ->andReturn('text/html');

                $response = $this->createResponse();

                // Create a mock for the Throwable
                $exception = Mockery::mock(Exception::class);

                $setting = new ErrorSetting(
                    displayErrorDetails: true,
                );

                // Create an instance of the HtmlErrorResponseRender class
                $errorRender = new HtmlErrorResponseRender();

                $actual = $errorRender->render($request, $response, $exception, $setting);

                expect($actual->getHeaderLine('Content-Type'))->toBe('text/html');
            }
        );

        it(
            'should not create and return a response renderer with HTML content',
            function () {
                /** @var AppTestCase $this */

                // Create a mock for the ServerRequestInterface
                $request = Mockery::mock(ServerRequestInterface::class);
                $request->shouldReceive('getHeaderLine')
                    ->with('Accept')
                    ->andReturn('application/json');

                $response = $this->createResponse();

                // Create a mock for the Throwable
                $exception = Mockery::mock(Exception::class);

                $setting = new ErrorSetting(
                    displayErrorDetails: true,
                );

                // Create an instance of the HtmlErrorResponseRender class
                $errorRender = new HtmlErrorResponseRender();

                $actual = $errorRender->render($request, $response, $exception, $setting);

                expect($actual)->toBeNull();
            }
        );
    }
)->group('html-error-response-render', 'error-handler');
