<?php

// Application basic configuration

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Console\Command\ServeCommand;

return [
    // Application base url
    'url' => env('APP_URL', 'http://localhost:8080'),

    // Application name
    'name' => env('APP_NAME', Application::Name),

    // Application environment
    'env' => env('APP_ENV', 'local'),

    // Is debug mode enabled?
    'debug' => (bool) env('APP_DEBUG', true),

    // Timezone
    'timezone' => 'Asia/Tokyo',

    // Built-in server flag
    'built_in_server' => (bool) env(
        ServeCommand::BuiltInServerEnvironment,
        // If the server is running in the command line, it is a built-in server
        php_sapi_name() === 'cli-server'
    ),
];
