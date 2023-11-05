<?php

define('APP_START_TIME', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$http = new Takemo101\Chubby\Http(
    Takemo101\Chubby\ApplicationBuilder::buildStandard(
        Takemo101\Chubby\ApplicationOption::from(
            basePath: getenv('APP_BASE_PATH') ?: dirname(__DIR__),
        ),
    ),
);

// Functionality can be expanded by adding instances of classes that implement Provider.
// $http->addProvider(ExampleProvider::class);

$http->run();
