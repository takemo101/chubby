<?php

use Takemo101\Chubby\Config\ConfigPhpRepository;
use Tests\Config\ConfigTestCase;

describe(
    'ConfigPhpRepository',
    function () {
        test(
            'load and retrieve settings for target directory',
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
            'verify existence of configuration key in target directory',
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
            'change the loaded settings of the target directory',
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
            'load settings from another directory',
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
            'retrieve configuration data from a specified path',
            function () {
                /** @var ConfigTestCase $this */

                $path = dirname(__DIR__, 1) . '/resource/config/config01.php';

                $expected = require $path;

                $actual = ConfigPhpRepository::getConfigByPath($path);

                expect($actual)->toEqual($expected);
            }
        );

        test(
            'settings loaded will overwrite the original settings',
            function () {
                /** @var ConfigTestCase $this */

                $configKeys = [
                    'config01.foo',
                    'config01.bar',
                    'config01.num',
                ];

                $exceptedConfigs = [];

                foreach ($configKeys as $key) {
                    $exceptedConfigs[$key] = $this->repository->get($key);
                }

                $this->repository->load($this->getAnotherDirectoryPath(), true);

                foreach ($configKeys as $key) {
                    expect($this->repository->get($key))->not->toEqual($exceptedConfigs[$key]);
                }
            },
        );

        test(
            'loaded settings do not overwrite original settings',
            function () {
                /** @var ConfigTestCase $this */

                $configKeys = [
                    'config01.foo',
                    'config01.bar',
                    'config01.num',
                ];

                $exceptedConfigs = [];

                foreach ($configKeys as $key) {
                    $exceptedConfigs[$key] = $this->repository->get($key);
                }

                $this->repository->load($this->getAnotherDirectoryPath(), false);

                foreach ($configKeys as $key) {
                    expect($this->repository->get($key))->toEqual($exceptedConfigs[$key]);
                }
            },
        );

        test(
            'Set the value of config',
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

        test(
            'merge config values',
            function (array $expected) {
                $repository = new ConfigPhpRepository();

                foreach ($expected as $key => $value) {
                    $repository->merge($key, $value);
                }

                expect($repository->all())->toEqual($expected);
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

describe(
    'ConfigPhpRepository::merge',
    function () {

        $config = [
            'app' => [
                'name' => 'MyApp',
                'debug' => true,
            ],
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
            ],
        ];

        test(
            'should merge data for the specified key',
            function () use ($config) {
                $key = 'app';
                $value = [
                    'debug' => false,
                    'timezone' => 'UTC',
                ];

                $repository = new ConfigPhpRepository();
                $repository->set($key, $config[$key]);

                $repository->merge($key, $value);

                $expected = [
                    'name' => 'MyApp',
                    'debug' => false,
                    'timezone' => 'UTC',
                ];

                $actual = $repository->get($key);

                expect($actual)->toBe($expected);
            }
        );

        test(
            'should merge data for the specified key without overwriting existing values',
            function () use ($config) {
                $key = 'database';
                $value = [
                    'host' => 'test',
                    'port' => 3308,
                    'username' => 'root',
                    'password' => 'password',
                ];

                $repository = new ConfigPhpRepository();
                $repository->set($key, $config[$key]);

                $repository->merge($key, $value, false);

                $expected = [
                    'host' => 'localhost',
                    'port' => 3306,
                    'username' => 'root',
                    'password' => 'password',
                ];

                $actual = $repository->get($key);

                expect($actual)->toBe($expected);
            }
        );
    }
)->group('config');
