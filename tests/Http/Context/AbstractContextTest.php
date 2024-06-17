<?php

use Takemo101\Chubby\Http\Context\AbstractContext;
use Takemo101\Chubby\Http\Context\RequestContextException;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\Context\RequestContext;

describe(
    'AbstractContext',
    function () {

        test(
            'can add context to request',
            function () {
                $context = new class() extends AbstractContext
                {
                    public function __construct()
                    {
                        // ...
                    }
                };

                $request = Mockery::mock(ServerRequestInterface::class);

                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(RequestContext::ContextKey)
                    ->andReturn(new RequestContext());

                $actual = $context->withRequest($request);

                expect($actual)->toBe($request);
            }
        );

        test(
            'can create context instance from server request',
            function () {
                $context = new class() extends AbstractContext
                {
                    public function __construct()
                    {
                        // ...
                    }
                };

                $request = Mockery::mock(ServerRequestInterface::class);

                $requestContext = Mockery::mock(RequestContext::class);
                $requestContext->shouldReceive('get')
                    ->once()
                    ->with(AbstractContext::ContextKey)
                    ->andReturn($context);

                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(RequestContext::ContextKey)
                    ->andReturn($requestContext);;

                $actual = AbstractContext::fromRequest($request);

                expect($actual)->toBe($context);
            }
        );

        test(
            'return null when context not found',
            function () {
                $request = Mockery::mock(ServerRequestInterface::class);

                $requestContext = Mockery::mock(RequestContext::class);
                $requestContext->shouldReceive('get')
                    ->once()
                    ->with(AbstractContext::ContextKey)
                    ->andReturn(null);

                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(RequestContext::ContextKey)
                    ->andReturn($requestContext);;

                expect(AbstractContext::fromRequest($request))
                    ->toBeNull();
            }
        );

        test(
            'throws exception when context is not an instance of the expected class',
            function () {
                $context = new class()
                {
                    public function __construct()
                    {
                        // ...
                    }
                };

                $request = Mockery::mock(ServerRequestInterface::class);

                $requestContext = Mockery::mock(RequestContext::class);
                $requestContext->shouldReceive('get')
                    ->once()
                    ->with(AbstractContext::ContextKey)
                    ->andReturn($context);

                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(RequestContext::ContextKey)
                    ->andReturn($requestContext);;


                expect(function () use ($request) {
                    AbstractContext::fromRequest($request);
                })->toThrow(RequestContextException::class);
            }
        );
    }
)->group('AbstractContext', 'http');
