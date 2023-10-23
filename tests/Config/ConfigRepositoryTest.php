<?php

use Takemo101\Chubby\Config\ConfigPhpRepository;
use Tests\Config\ConfigTestCase;

describe(
    'config',
    function () {
        test(
            'Load and retrieve settings for target directory',
            function (string $configKey, mixed $excepted) {
                /** @var ConfigTestCase $this */

                expect($this->repository->get($configKey))->toEqual($excepted);
                expect($this->repository[$configKey])->toEqual($excepted);
            },
        )->with(
            [
                ['config01.foo', 'bar'],
                ['config01.bar', 'baz'],
                ['config01.num', 123],
                ['config02.test.foo', 'bar'],
                ['config02.test.bar', 'baz'],
                ['config02.test.num', 123],
            ]
        );

        test(
            'Verify existence of configuration key in target directory',
            function (string $configKey) {
                /** @var ConfigTestCase $this */

                expect($this->repository->has($configKey))->toBeTrue();
                expect(isset($this->repository[$configKey]))->toBeTrue();
            },
        )->with(
            [
                'config01.foo',
                'config01.bar',
                'config01.num',
                'config02.test.foo',
                'config02.test.bar',
                'config02.test.num',
            ]
        );

        test(
            'Change the loaded settings of the target directory',
            function (string $configKey, mixed $changed) {
                /** @var ConfigTestCase $this */

                $original = $this->repository->get($configKey);

                $this->repository->set($configKey, $changed);

                $actual = $this->repository->get($configKey);

                expect($actual)->not->toEqual($original);

                // Reset the original value and verify

                $this->repository[$configKey] = $original;

                $actual = $this->repository->get($configKey);

                expect($actual)->toEqual($original);
            },
        )->with(
            [
                ['config01.foo', 1],
                ['config01.bar', 2],
                ['config01.num', 3],
                ['config01.test.bool', true],
                ['config02.test.foo', 1],
                ['config02.test.bar', 2],
                ['config02.test.num', 3],
                ['config02.test.bool', true],
            ]
        );

        test(
            'Load settings from another directory',
            function (string $configKey, mixed $excepted) {
                /** @var ConfigTestCase $this */

                $this->repository->load($this->getAnotherDirectoryPath());

                expect($this->repository->get($configKey))->toEqual($excepted);
            },
        )->with(
            [
                ['another-config.foo', 'bar'],
                ['another-config.bar', 'baz'],
            ]
        );

        test(
            '',
            function (array $excepted) {
                $repository = new ConfigPhpRepository();

                foreach ($excepted as $key => $value) {
                    $repository->set($key, $value);
                }

                expect($repository->all())->toEqual($excepted);
            },
        )->with([
            fn () => [
                'hoge' => [
                    'fuga' => 'piyo',
                ],
                'foo' => ['bar'],
            ],
            fn () => [
                'test' => [
                    'test' => 'test',
                ],
                'test01' => ['test'],
                'test02' => ['test'],
            ],
        ]);
    }
)->group('config');
