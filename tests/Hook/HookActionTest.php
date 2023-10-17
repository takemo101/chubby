<?php

use Takemo101\Chubby\Hook\HookAction;

describe(
    'hook action',
    function () {
        test(
            'Create a unique key from a string that is a callable',
            function (string $function) {
                $action = new HookAction($function);

                expect($action->getUniqueKey())->toEqual($function);
            },
        )->with([
            'strlen',
            'is_string',
            'is_array',
        ]);

        test(
            'Create unique key from Closure',
            function (Closure $function) {
                $first = new HookAction($function);
                $second = new HookAction($function);

                // The same unique key is generated from the same object
                expect($first->getUniqueKey())->toEqual($second->getUniqueKey());
            },
        )->with([
            fn () => fn () => 'a',
            fn () => fn () => 'b',
            fn () => fn () => 'c',
        ]);

        test(
            'Create a unique key from an array that is a callable',
            function (array $function) {
                $first = new HookAction($function);
                $second = new HookAction($function);

                // The same unique key is generated from the same callable
                expect($first->getUniqueKey())->toEqual($second->getUniqueKey());
            },
        )->with([
            [
                [
                    new class()
                    {
                        public function __invoke()
                        {
                            return 'a';
                        }
                    },
                    '__invoke',
                ]
            ],
            [
                [
                    new class()
                    {
                        public function __invoke()
                        {
                            return 'b';
                        }
                    },
                    '__invoke',
                ]
            ],
        ]);
    }
)->group('hook');
