<?php


define('APP_START_TIME', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

$app = Takemo101\Chubby\ApplicationBuilder::buildStandard(
    Takemo101\Chubby\ApplicationOption::from(
        basePath: getenv('APP_BASE_PATH') ?: __DIR__,
    ),
);

// Functionality can be expanded by adding instances of classes that implement Provider.
// $app->addProvider(ExampleProvider::class);

$app->boot();

/** @var Takemo101\Chubby\Http\DomainRouter */
$router = $app->get(Takemo101\Chubby\Http\DomainRouter::class);

$router->run();
