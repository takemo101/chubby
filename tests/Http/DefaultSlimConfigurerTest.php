<?php

use Psr\EventDispatcher\EventDispatcherInterface;
use Slim\App as Slim;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Event\AfterAddRoutingMiddleware;
use Takemo101\Chubby\Http\Event\AfterSlimConfiguration;
use Takemo101\Chubby\Http\Event\BeforeAddRoutingMiddleware;
use Takemo101\Chubby\Http\Event\BeforeSlimConfiguration;
use Takemo101\Chubby\Http\GlobalMiddlewareCollection;
use Takemo101\Chubby\Http\Middleware\StartContext;
use Takemo101\Chubby\Http\Configurer\DefaultSlimConfigurer;
use Mockery as m;
use Psr\Http\Server\MiddlewareInterface;

beforeEach(function () {
    $this->middlewares = m::mock(GlobalMiddlewareCollection::class);
    $this->dispatcher = m::mock(EventDispatcherInterface::class);
    $this->hook = m::mock(Hook::class);
    $this->basePath = '/path/to/base';
    $this->configurer = new DefaultSlimConfigurer(
        $this->middlewares,
        $this->dispatcher,
        $this->hook,
        $this->basePath
    );
});

describe(
    'DefaultSlimConfigurer',
    function () {
        it('should configure the Slim application', function () {
            $slim = m::mock(Slim::class);
            $middleware1 = m::mock(MiddlewareInterface::class);
            $middleware2 = m::mock(MiddlewareInterface::class);

            $this->hook->shouldReceive('do')
                ->once()
                ->with(ApplicationHookTags::Http_BeforeSlimConfiguration, $slim);

            $this->dispatcher->shouldReceive('dispatch')
                ->once()
                ->with(m::type(BeforeSlimConfiguration::class));

            $this->middlewares->shouldReceive('classes')
                ->once()
                ->andReturn([$middleware1, $middleware2]);

            $slim->shouldReceive('add')
                ->once()
                ->with($middleware1);

            $slim->shouldReceive('add')
                ->once()
                ->with($middleware2);

            $slim->shouldReceive('setBasePath')
                ->with($this->basePath);

            $this->hook->shouldReceive('do')
                ->once()
                ->with(ApplicationHookTags::Http_BeforeAddRoutingMiddleware, $slim);

            $this->dispatcher->shouldReceive('dispatch')
                ->once()
                ->with(m::type(BeforeAddRoutingMiddleware::class));

            $slim->shouldReceive('addRoutingMiddleware')
                ->once();

            // Mock the hook and event dispatcher calls
            $this->hook->shouldReceive('do')
                ->once()
                ->with(ApplicationHookTags::Http_AfterAddRoutingMiddleware, $slim);

            $this->dispatcher->shouldReceive('dispatch')
                ->once()
                ->with(m::type(AfterAddRoutingMiddleware::class));

            $slim->shouldReceive('add')
                ->once()
                ->with(BodyParsingMiddleware::class);

            $slim->shouldReceive('add')
                ->once()
                ->with(ErrorMiddleware::class);

            $slim->shouldReceive('add')
                ->once()
                ->with(StartContext::class);

            $this->hook->shouldReceive('do')
                ->once()
                ->with(ApplicationHookTags::Http_AfterSlimConfiguration, $slim);

            $this->dispatcher->shouldReceive('dispatch')
                ->once()
                ->with(m::type(AfterSlimConfiguration::class));

            $result = $this->configurer->configure($slim);

            expect($result)->toBe($slim);
        });
    }
)->group('DefaultSlimConfigurer');
