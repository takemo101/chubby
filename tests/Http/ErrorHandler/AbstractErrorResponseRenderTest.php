<?php

use Takemo101\Chubby\Http\ErrorHandler\AbstractErrorResponseRender;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\ErrorHandler\ErrorSetting;
use Takemo101\Chubby\Http\Renderer\ResponseRenderer;

describe(
    'AbstractErrorResponseRender',
    function () {

        test(
            'should return null if shouldRender returns false',
            function () {
                $request = Mockery::mock(ServerRequestInterface::class);
                $response = Mockery::mock(ResponseInterface::class);
                $exception = new Exception();
                $setting = new ErrorSetting();

                $render = Mockery::mock(AbstractErrorResponseRender::class)->makePartial();
                $render->shouldAllowMockingProtectedMethods();
                $render->shouldReceive('shouldRender')->andReturn(false);

                $actual = $render->render($request, $response, $exception, $setting);

                expect($actual)->toBeNull();
            }
        );

        test(
            'should call createRenderer and render methods if shouldRender returns true',
            function () {
                $request = Mockery::mock(ServerRequestInterface::class);
                $response = Mockery::mock(ResponseInterface::class);
                $exception = new Exception();
                $setting = new ErrorSetting();

                $renderer = Mockery::mock(ResponseRenderer::class);
                $renderer->shouldReceive('render')->andReturn($response);

                $render = Mockery::mock(AbstractErrorResponseRender::class)->makePartial();
                $render->shouldAllowMockingProtectedMethods();
                $render->shouldReceive('shouldRender')->andReturn(true);
                $render->shouldReceive('createRenderer')->andReturn($renderer);

                $actual = $render->render($request, $response, $exception, $setting);

                expect($actual)->toBe($response);
            }
        );
    }
)->group('abstract-error-response-render', 'error-handler');
