<?php

use Takemo101\Chubby\Context\Context;
use Takemo101\Chubby\Context\NotFoundContextException;
use Takemo101\Chubby\Context\SingleContextRepository;

beforeEach(function () {
    $this->repository = new SingleContextRepository();
});

describe(
    'SingleContextRepository',
    function () {

        it('should return the set context', function () {
            $context = new Context();
            $this->repository->set($context);
            $result = $this->repository->get();
            expect($result)->toBe($context);
        });

        it('should throw an exception when getting context if not set', function () {
            $this->expectException(NotFoundContextException::class);
            $this->repository->get();
        });

        it('should return true if context is set', function () {
            $context = new Context();
            $this->repository->set($context);
            $result = $this->repository->has();
            expect($result)->toBeTrue();
        });

        it('should return false if context is not set', function () {
            $result = $this->repository->has();
            expect($result)->toBeFalse();
        });

        it('should throw an exception when setting context if already set', function () {
            $context1 = new Context();
            $context2 = new Context();
            $this->repository->set($context1);
            $this->expectException(LogicException::class);
            $this->repository->set($context2);
        });

        it('should return the process ID', function () {
            $result = $this->repository->cid();
            expect($result)->toBeString();
        });
    }
)->group('SingleContextRepository', 'context');
