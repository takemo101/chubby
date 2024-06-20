<?php

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Event\RequestReceived;
use Takemo101\Chubby\Http\Event\ResponseSending;
use Takemo101\Chubby\Http\Middleware\RequestHookSetup;
use Mockery as m;

beforeEach(function () {
    $this->dispatcher = m::mock(EventDispatcherInterface::class);
    $this->hook = m::mock(Hook::class);
    $this->request = m::mock(ServerRequestInterface::class);
    $this->handler = m::mock(RequestHandlerInterface::class);
    $this->response = m::mock(ResponseInterface::class);

    $this->middleware = new RequestHookSetup($this->dispatcher, $this->hook);
});

describe(
    'RequestHookSetup',
    function () {
        it('should process the request and response', function () {
            $this->hook->shouldReceive('do')
                ->once()
                ->with(ServerRequestInterface::class, $this->request)
                ->andReturn($this->request);

            $this->hook->shouldReceive('do')
                ->once()
                ->with(ApplicationHookTags::Http_RequestReceived, $this->request)
                ->andReturn($this->request);

            $this->dispatcher->shouldReceive('dispatch')
                ->once()
                ->with(m::type(RequestReceived::class));

            $this->handler->shouldReceive('handle')
                ->once()
                ->with($this->request)
                ->andReturn($this->response);

            $this->hook->shouldReceive('do')
                ->once()
                ->with(ResponseInterface::class, $this->response)
                ->andReturn($this->response);

            $this->hook->shouldReceive('do')
                ->once()
                ->with(ApplicationHookTags::Http_ResponseSending, $this->response)
                ->andReturn($this->response);

            $this->dispatcher->shouldReceive('dispatch')
                ->once()
                ->with(m::type(ResponseSending::class));

            $result = $this->middleware->process($this->request, $this->handler);

            expect($result)->toBe($this->response);
        });
    }
)->group('RequestHookSetup', 'middleware');
