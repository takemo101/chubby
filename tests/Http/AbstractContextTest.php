<?php

use Takemo101\Chubby\Http\Support\AbstractContext;
use Takemo101\Chubby\Http\Support\ContextException;
use Psr\Http\Message\ServerRequestInterface;

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
                $request->shouldReceive('withAttribute')
                    ->once()
                    ->with(AbstractContext::ContextKey, $context)
                    ->andReturn($request);

                $actual = $context->withContext($request);

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
                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(AbstractContext::ContextKey)
                    ->andReturn($context);

                $actual = AbstractContext::fromRequest($request);

                expect($actual)->toBe($context);
            }
        );

        test(
            'return null when context not found',
            function () {
                $request = Mockery::mock(ServerRequestInterface::class);
                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(AbstractContext::ContextKey)
                    ->andReturn(null);

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
                $request->shouldReceive('getAttribute')
                    ->once()
                    ->with(AbstractContext::ContextKey)
                    ->andReturn($context);

                expect(function () use ($request) {
                    AbstractContext::fromRequest($request, function () {
                        return new class() extends AbstractContext
                        {
                            public function __construct()
                            {
                                // ...
                            }
                        };
                    });
                })->toThrow(ContextException::class);
            }
        );
    }
)->group('AbstractContext', 'http');
