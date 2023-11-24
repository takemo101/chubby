<?php

use PHPUnit\Framework\TestStatus\Error;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRender;
use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRenders;
use Takemo101\Chubby\Http\ErrorHandler\ErrorHandler;
use Takemo101\Chubby\Http\ErrorHandler\ErrorSetting;
use Mockery as m;

beforeEach(function () {
    $this->responseFactory = m::mock(ResponseFactoryInterface::class);
    $this->logger = m::mock(LoggerInterface::class);
});

describe(
    'ErrorHandler',
    function () {

        it(
            'should add ErrorResponseRender',
            function () {
                $render = m::mock(ErrorResponseRender::class);

                $errorHandler = new ErrorHandler(
                    $this->responseFactory,
                    $this->logger,
                    new ErrorResponseRenders(),
                );

                $errorHandler->addRender($render);

                $actual = $errorHandler->getRender(get_class($render));

                expect($actual)->toBe($render);
            }
        );

        it(
            'should set ErrorResponseRender',
            function () {
                $render = m::mock(ErrorResponseRender::class);

                $errorHandler = new ErrorHandler(
                    $this->responseFactory,
                    $this->logger,
                    new ErrorResponseRenders(),
                );

                $errorHandler->setRender($render);

                $actual = $errorHandler->getRender(get_class($render));

                expect($actual)->toBe($render);
            }
        );

        it(
            'should get ErrorResponseRender',
            function () {
                $render = m::mock(ErrorResponseRender::class);

                $errorHandler = new ErrorHandler(
                    $this->responseFactory,
                    $this->logger,
                    new ErrorResponseRenders($render),
                );

                $actual = $errorHandler->getRender(get_class($render));

                expect($actual)->toBe($render);
            }
        );

        it(
            'should invoke error handling process',
            function () {
                $request = m::mock(ServerRequestInterface::class);
                $exception = new Exception();
                $displayErrorDetails = true;
                $logErrors = true;
                $logErrorDetails = true;
                $response = m::mock(ResponseInterface::class);

                $errorHandler = m::mock(ErrorHandler::class)
                    ->shouldAllowMockingProtectedMethods()
                    ->makePartial();

                $errorHandler->shouldReceive('createResponse')
                    ->andReturn($response);
                $errorHandler->shouldReceive('report');
                $errorHandler->shouldReceive('render')
                    ->andReturn($response);

                $actual = $errorHandler->__invoke($request, $exception, $displayErrorDetails, $logErrors, $logErrorDetails);

                expect($actual)->toBe($response);
            }
        );
    }
)->group('error-handling', 'error-handler');
