<?php

// Application basic configuration

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Console\Command\ServeCommand;

return [
    // Application name
    'name' => env('APP_NAME', Application::Name),

    // Application environment
    'env' => env('APP_ENV', 'local'),

    // Is debug mode enabled?
    'debug' => (bool) env('APP_DEBUG', true),

    // Timezone
    'timezone' => 'Asia/Tokyo',

    // Built-in server flag
    'built_in_server' => (bool) env(ServeCommand::BuiltInServerEnvironment, false),
];
