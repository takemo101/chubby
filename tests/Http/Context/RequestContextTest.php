<?php

use Takemo101\Chubby\Http\Context\RequestContext;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\Context\ContextException;
use Mockery as m;

describe(
    'RequestContext',
    function () {
        it('sets and gets a value', function () {
            $context = new RequestContext();
            $context->set('key', 'value');

            $result = $context->get('key');

            expect($result)->toBe('value');
        });

        it('returns default value if key does not exist', function () {
            $context = new RequestContext();

            $result = $context->get('nonexistent', 'default');

            expect($result)->toBe('default');
        });

        it('sets and gets a typed value', function () {
            $context = new RequestContext();
            $object = new stdClass();

            $context->setTyped($object);

            $result = $context->getTyped(stdClass::class);

            expect($result)->toBe($object);
        });

        it('throws exception when getting non-matching typed value', function () {
            $context = new RequestContext();
            $context->set('key', 'value');

            expect(function () use ($context) {
                $context->getTyped(stdClass::class);
            })->toThrow(InvalidArgumentException::class);
        });

        it('sets aliases for a value', function () {
            $context = new RequestContext();
            $context->set('key', 'value');

            $context->setAliases('key', ['alias1', 'alias2']);

            $result1 = $context->get('alias1');
            $result2 = $context->get('alias2');

            expect($result1)->toBe('value');
            expect($result2)->toBe('value');
        });

        it('throws exception when setting aliases for non-existing value', function () {
            $context = new RequestContext();

            expect(function () use ($context) {
                $context->setAliases('nonexistent', ['alias1', 'alias2']);
            })->toThrow(InvalidArgumentException::class);
        });

        it('returns all values', function () {
            $context = new RequestContext();
            $context->set('key1', 'value1');
            $context->set('key2', 'value2');

            $result = $context->values();

            expect($result)->toEqual(['key1' => 'value1', 'key2' => 'value2', RequestContext::class => $context]);
        });

        it('checks if the specified identifier exists', function () {
            $context = new RequestContext();
            $context->set('key', 'value');

            $result = $context->has('key');

            expect($result)->toBe(true);
        });

        it('checks if the specified identifier does not exist', function () {
            $context = new RequestContext();

            $result = $context->has('nonexistent');

            expect($result)->toBe(false);
        });

        it('checks if the specified object type exists', function () {
            $context = new RequestContext();
            $object = new stdClass();
            $context->setTyped($object);

            $result = $context->hasTyped($object);

            expect($result)->toBe(true);
        });

        it('checks if the specified object type does not exist', function () {
            $context = new RequestContext();
            $object = new stdClass();

            $result = $context->hasTyped($object);

            expect($result)->toBe(false);
        });

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
            })->toThrow(ContextException::class);
        });

        it('throws exception when request context is not an instance of RequestContext', function () {
            $request = m::mock(ServerRequestInterface::class);
            $request->shouldReceive('getAttribute')->with(RequestContext::ContextKey)->andReturn(new stdClass());

            expect(function () use ($request) {
                RequestContext::fromRequest($request);
            })->toThrow(ContextException::class);
        });
    }
)->group('RequestContext');
