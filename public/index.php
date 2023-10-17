<?php

define('APP_START_TIME', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$http = Takemo101\Chubby\Http\Http::create(
    Takemo101\Chubby\ApplicationOption::from(
        basePath: $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__),
    ),
);

// Functionality can be expanded by adding instances of classes that implement Provider.
// $http->addProvider(ExampleProvider::class);

$http->run();
