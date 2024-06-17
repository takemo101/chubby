<?php

use Takemo101\Chubby\Http\Context\RequestContext;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\Context\RequestContextException;
use Mockery as m;

describe(
    'RequestContext',
    function () {

        it('returns the request context from a server request', function () {
            $context = new RequestContext();
            $request = m::mock(ServerRequestInterface::class);
            $request->shouldReceive('getAttribute')->with(RequestContext::ContextKey)->andReturn($context);

            $result = RequestContext::fromRequest($request);

            expect($result)->toBe($context);
        });

        it('throws exception when request context is not set', function () {
            $request = m::mock(ServerRequestInterface::class);
            $request->shouldReceive('getAttribute')->with(RequestContext::ContextKey)->andReturn(null);

            expect(function () use ($request) {
                RequestContext::fromRequest($request);
            })->toThrow(RequestContextException::class);
        });

        it('throws exception when request context is not an instance of RequestContext', function () {
            $request = m::mock(ServerRequestInterface::class);
            $request->shouldReceive('getAttribute')->with(RequestContext::ContextKey)->andReturn(new stdClass());

            expect(function () use ($request) {
                RequestContext::fromRequest($request);
            })->toThrow(RequestContextException::class);
        });
    }
)->group('RequestContext');
