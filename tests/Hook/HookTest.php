<?php

use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Hook\Hook;

describe(
    'Hook',
    function () {
        test(
            'Set filter or action processing for tags',
            function (string $tag, callable $function) {
                $hook = new Hook();

                $hook->on($tag, $function);

                expect($hook->hasTag($tag))->toBeTrue();
            },
        )->with([
            ['tag01', fn (string $data) => $data],
            ['tag02', new HookTestProcess()],
            ['tag03', [new HookTestProcess(), '__invoke']],
            [HookTestProcess::class, fn (HookTestProcess $data) => $data],
        ]);

        test(
            'Set filter or action processing based on action argument type',
            function () {

                $object = new HookTestProcess();

                $hook = new Hook();

                $hook->onTyped(function (HookTestProcess $object, ContainerInterface $container) {
                    return $container->get(HookTestProcess::class);
                });

                expect($hook->hasTag(get_class($object)))->toBeTrue();
            },
        );

        test(
            'Delete all filters or actions for tags',
            function () {

                $tag = 'tag';

                $actions = [
                    fn (string $data) => $data,
                    fn (string $data) => $data,
                    fn (string $data) => $data,
                    fn (string $data) => $data,
                ];

                $hook = new Hook();

                foreach ($actions as $action) {
                    $hook->on($tag, $action);
                }

                expect($hook->hasTag($tag))->toBeTrue();

                $hook->remove($tag);

                expect($hook->hasTag($tag))->not->toBeTrue();
            },
        );

        test(
            'Perform filtering on tags',
            function ($function) {
                $tag = 'tag';
                $count = 4;

                $hook = new Hook();

                foreach (range(0, $count) as $index) {
                    $hook->on($tag, $function, $index);
                }

                $data = $expected = 1;

                $actual = $hook->do($tag, $data);

                foreach (range(0, $count) as $index) {
                    $expected = call_user_func($function, $expected);
                }

                expect($actual)->toEqual($expected);
            }
        )->with([
            [fn () => fn (int $data) => $data + 1],
            [new HookTestProcess()],
            [[new HookTestProcess(), '__invoke']],
        ]);

        test(
            'Perform actions on tag',
            function ($data, $process) {
                $tag = 'tag';
                $count = 4;

                $hook = new Hook();

                $data = $actual = $expected = 1;

                $function = function () use (&$actual, $process) {
                    $actual = call_user_func($process, $actual);
                };

                foreach (range(0, $count) as $index) {
                    $hook->on($tag, $function, $index);
                }

                $hook->do($tag, $data);

                foreach (range(0, $count) as $index) {
                    $expected = call_user_func($process, $expected);
                }

                expect($actual)->toEqual($expected);
            }
        )->with([
            ['hello', fn () => fn (string $data) => $data .= 'test'],
            [1, fn () => fn (string $data) => $data += 1]
        ]);

        test(
            'Perform filtering on object types',
            function (HookTestData $data) {
                $hook = new Hook();

                $expected = clone $data;

                $function = function (HookTestData $data) {
                    $data->data += 1;

                    return $data;
                };

                $hook->onTyped($function);

                $actual = $hook->doTyped($data);

                $expected = call_user_func($function, $expected);

                expect($actual->data)->toEqual($expected->data);
            }
        )->with([
            new HookTestData(1),
            new HookTestData(20),
        ]);

        test(
            'Perform actions on object types',
            function (HookTestData $data) {
                $hook = new Hook();

                $expected = clone $data;

                $function = function (HookTestData $data) {
                    $data->data = 10;
                };

                $hook->onTyped($function);

                $hook->doTyped($data);

                call_user_func($function, $expected);

                expect($data->data)->toEqual($expected->data);
            }
        )->with([
            new HookTestData(1),
            new HookTestData(20),
        ]);
    }
)->group('hook');

class HookTestProcess
{
    public function __invoke(int $data)
    {
        return $data + 1;
    }
}

class HookTestData
{
    public function __construct(public int $data)
    {
        //
    }
}
