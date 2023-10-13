<?php

// Log basic settings

use Monolog\Level;

return [
    // Log channel name
    'name' => null,

    // Log directory path
    'path' => storage_path('logs'),

    // Log file name
    'filename' => 'error.log',

    // Log level
    'level' => Level::Debug,
];
