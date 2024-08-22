<?php

use Takemo101\Chubby\Exception\Exceptions;

describe(
    'Exceptions',
    function () {

        it('can be instantiated with multiple throwables', function () {
            $throwable1 = new Exception('Exception 1');
            $throwable2 = new Exception('Exception 2');

            $exceptions = new Exceptions($throwable1, $throwable2);

            expect($exceptions)->toBeInstanceOf(Exceptions::class);
            expect($exceptions->getThrowables())->toEqual([$throwable1, $throwable2]);
        });

        it('throws an exception when instantiated without any throwables', function () {

            ini_set('zend.assertions', 1);

            expect(
                function () {
                    new Exceptions();
                }
            )->toThrow(AssertionError::class);
        });

        it('returns the correct throwables when instantiated with multiple throwables', function () {
            $throwable1 = new Exception('Exception 1');
            $throwable2 = new Exception('Exception 2');

            $exceptions = new Exceptions($throwable1, $throwable2);

            expect($exceptions->getThrowables())->toEqual([$throwable1, $throwable2]);
        });
    },
)->group('Exceptions', 'exception');
