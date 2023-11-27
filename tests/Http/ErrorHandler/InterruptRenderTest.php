<?php

use Takemo101\Chubby\Http\ErrorHandler\InterruptRender;
use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mockery as m;

describe(
    'InterruptRender',
    function () {
        it('renders the response using the provided renderer', function () {
            $renderer = m::mock(ResponseRenderer::class);
            $request = m::mock(ServerRequestInterface::class);
            $response = m::mock(ResponseInterface::class);

            $renderer->shouldReceive('render')
                ->once()
                ->with($request, $response)
                ->andReturn($response);

            $interruptRender = new InterruptRender($renderer);

            $result = $interruptRender->render($request, $response);

            expect($result)->toBe($response);
        });

        it('creates an instance from a response', function () {
            $response = m::mock(ResponseInterface::class);

            $interruptRender = InterruptRender::fromResponse($response);

            expect($interruptRender)->toBeInstanceOf(InterruptRender::class);
            expect(
                $interruptRender->render(
                    m::mock(ServerRequestInterface::class),
                    $response,
                ),
            )->toBe($response);
        });
    },
)->group('interrupt-render', 'error-handler');
