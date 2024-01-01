<?php

// Application basic configuration

use Takemo101\Chubby\Application;

return [
    // Application name
    'name' => env('APP_NAME', Application::Name),

    // Application environment
    'env' => env('APP_ENV', 'local'),

    // Is debug mode enabled?
    'debug' => (bool) env('APP_DEBUG', true),

    // Timezone
    'timezone' => 'Asia/Tokyo',
];
