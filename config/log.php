<?php

// Log basic settings

use Monolog\Level;
use Takemo101\Chubby\Log\Factory\ConsoleHandlerFactory;

return [
    // Log channel name
    'name' => null,

    // Log directory path
    'path' => storage_path('logs'),

    // Log file name
    'filename' => 'error.log',

    // Log stream path
    'stream' => ConsoleHandlerFactory::DefaultStream,

    // Log level
    'level' => Level::Debug,
];
