<?php

use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRenders;
use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRender;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\ErrorHandler\ErrorSetting;
use Tests\AppTestCase;

describe(
    'ErrorResponseRenders',
    function () {

        test(
            'can add ErrorResponseRender',
            function () {
                $renders = new ErrorResponseRenders();
                $render = new MockErrorResponseRender();

                $renders->addRender($render);

                $actual = $renders->getRender(MockErrorResponseRender::class);

                expect($actual)->toBe($render);
            }
        );

        test(
            'can set ErrorResponseRender',
            function () {
                $renders = new ErrorResponseRenders();
                $render = new MockErrorResponseRender();

                $renders->setRender($render);

                $actual = $renders->getRender(MockErrorResponseRender::class);

                expect($actual)->toBe($render);
            }
        );

        test(
            'returns the response from the first ErrorResponseRender that can handle the exception',
            function () {
                $renders = new ErrorResponseRenders();

                $request = Mockery::mock(ServerRequestInterface::class);
                $response = Mockery::mock(ResponseInterface::class);
                $exception = new Exception();
                $setting = new ErrorSetting();

                $render = Mockery::mock(ErrorResponseRender::class);
                $render->shouldReceive('render')->andReturn($response);

                $renders->setRender($render);

                $actual = $renders->render($request, $response, $exception, $setting);

                expect($actual)->toBe($response);
            }
        );

        test(
            'returns the original response if no ErrorResponseRender can handle the exception',
            function (string $contentType) {
                /** @var AppTestCase $this */

                $renders = new ErrorResponseRenders();

                $request = Mockery::mock(ServerRequestInterface::class);

                $request->shouldReceive('getHeaderLine')->andReturn($contentType);

                $response = $this->createResponse();

                $exception = new Exception();
                $setting = new ErrorSetting();

                $renders->setRender();

                $actual = $renders->render($request, $response, $exception, $setting);

                expect($actual)->not->toBe($response);
                expect($actual->getHeaderLine('Content-Type'))->toBe($contentType);
            }
        )->with([
            'text/html',
            'application/json',
        ]);
    }
)->group('error-response-renders', 'error-handler');

class MockErrorResponseRender implements ErrorResponseRender
{
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Throwable $exception,
        ErrorSetting $setting
    ): ?ResponseInterface {
        return null;
    }
}
