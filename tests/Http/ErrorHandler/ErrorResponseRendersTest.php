<?php

use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRenders;
use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRender;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\ErrorHandler\ErrorSetting;

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
