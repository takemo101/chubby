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

        it('can add additional throwables', function () {
            $throwable1 = new Exception('Exception 1');
            $throwable2 = new Exception('Exception 2');
            $throwable3 = new Exception('Exception 3');

            $exceptions = new Exceptions($throwable1, $throwable2);
            $exceptions = $exceptions->addThrowables($throwable3);

            expect($exceptions)->toBeInstanceOf(Exceptions::class);
            expect($exceptions->getThrowables())->toEqual([$throwable1, $throwable2, $throwable3]);
        });

        it('has the correct message and code', function () {
            $exceptions = new Exceptions();

            expect($exceptions->getMessage())->toEqual('Multiple exceptions occurred');
            expect($exceptions->getCode())->toEqual(0);
        });
    },
)->group('Exceptions', 'exception');
