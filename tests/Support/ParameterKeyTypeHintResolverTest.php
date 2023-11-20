<?php

use Takemo101\Chubby\Support\ParameterKeyTypeHintResolver;

describe(
    'ParameterKeyTypeHintResolver',
    function () {

        test(
            'should resolve parameters with type hints',
            function () {
                $resolver = new ParameterKeyTypeHintResolver();

                $callable = function (string $param1, int $param2, stdClass $param3) {
                    return [$param1, $param2, $param3];
                };

                $parameters = [
                    'param1' => 'value1',
                    'param2' => 'value2',
                    'param3' => 'value3',
                ];

                $expected = [
                    'param1' => 'value1',
                    'param2' => 'value2',
                    'param3' => 'value3',
                ];

                $actual = $resolver->resolve($callable, $parameters);

                expect($actual)->toBe($expected);
            }
        );

        test('should resolve parameters with "self" type hint', function () {
            $resolver = new ParameterKeyTypeHintResolver();

            $callable = new class()
            {
                public function __invoke(
                    stdClass $param1,
                    self $param2,
                ) {
                    return [$param1, $param2];
                }
            };

            $parameters = [
                'stdClass' => 'value1',
                get_class($callable) => 'value2',
            ];

            $expected = [
                'param1' => 'value1',
                'param2' => 'value2',
            ];

            $actual = $resolver->resolve($callable, $parameters);

            expect($actual)->toBe($expected);
        });

        test(
            'should not resolve parameters without type hints',
            function () {
                $resolver = new ParameterKeyTypeHintResolver();

                $callable = function ($param1, $param2, $param3) {
                    return [$param1, $param2, $param3];
                };

                $parameters = [
                    'param1' => 'value1',
                    'param2' => 'value2',
                    'param3' => 'value3',
                ];

                $expected = [
                    'param1' => 'value1',
                    'param2' => 'value2',
                    'param3' => 'value3',
                ];

                $actual = $resolver->resolve($callable, $parameters);

                expect($actual)->toBe($expected);
            }
        );

        test(
            'should not resolve parameters with built-in type hints',
            function () {
                $resolver = new ParameterKeyTypeHintResolver();

                $callable = function (string $param1, int $param2) {
                    return [$param1, $param2];
                };

                $parameters = [
                    'param1' => 'value1',
                    'param2' => 'value2',
                ];

                $expected = [
                    'param1' => 'value1',
                    'param2' => 'value2',
                ];

                $actual = $resolver->resolve($callable, $parameters);

                expect($actual)->toBe($expected);
            }
        );
    }
)->group('type-hint-resolver', 'support');
