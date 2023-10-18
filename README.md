# Chubby
A package to easily use the slim framework.

## Installation
```bash
composer require takemo101/chubby
```

## Usage

``php -S localhost:8080 index.php``
```php
<?php

// index.php

use Psr\Http\Message\ResponseInterface;
use Takemo101\Chubby\Http;

require_once __DIR__ . '/vendor/autoload.php';

$http = Http::createSimple();

$http->get(
    '/books/{id}', 
    function (ResponseInterface $response, string $id) {
        $response->getBody()->write("ID = {$id}");

        return $response;
    },
);

$http->run();
```

``php console hello``
```php
#!/usr/bin/env php
<?php

// console

use Symfony\Component\Console\Output\OutputInterface;
use Takemo101\Chubby\Console\Command\CallableCommand;
use Takemo101\Chubby\Console;

require_once __DIR__ . '/vendor/autoload.php';

$console = Console::createSimple();

$console->addCommand(
    CallableCommand::from(
        function (OutputInterface $output) {

            $output->writeln('Hello World!');

            return CallableCommand::SUCCESS;
        },
    )->setName('hello'),
);

$console->run();
```
