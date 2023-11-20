<?php

use Takemo101\Chubby\Config\ConfigPhpRepository;

beforeEach(function () {
    $this->config = [
        'app' => [
            'name' => 'MyApp',
            'debug' => true,
        ],
        'database' => [
            'host' => 'localhost',
            'port' => 3306,
        ],
    ];

    $this->repository = new ConfigPhpRepository($this->config);
});

describe('ConfigPhpRepository::merge', function () {

    describe('merge', function () {
        it('should merge data for the specified key', function () {
            $key = 'app';
            $value = [
                'debug' => false,
                'timezone' => 'UTC',
            ];

            $this->repository->merge($key, $value);

            $expected = [
                'name' => 'MyApp',
                'debug' => false,
                'timezone' => 'UTC',
            ];

            expect($this->config[$key])->toBe($expected);
        });

        it('should merge data for the specified key without overwriting existing values', function () {
            $key = 'database';
            $value = [
                'username' => 'root',
                'password' => 'password',
            ];

            $this->repository->merge($key, $value, false);

            $expected = [
                'host' => 'localhost',
                'port' => 3306,
                'username' => 'root',
                'password' => 'password',
            ];

            expect($this->config[$key])->toBe($expected);
        });

        it('should merge data for the specified key when the current value is not an array', function () {
            $key = 'app.name';
            $value = 'MyApp v2';

            $this->repository->merge($key, $value);

            $expected = 'MyApp v2';

            expect($this->config['app']['name'])->toBe($expected);
        });
    });
});
