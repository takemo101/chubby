<?php

// Slim framework related settings

use Takemo101\Chubby\Http\ErrorHandler\ErrorHandler;

return [

    // Base path
    'base_path' => env('BASE_PATH'),

    // Error output settings
    'error' => [

        // ErrorHandler class
        'handler' => ErrorHandler::class,

        // Settings for ErrorMiddleware
        'setting' => [

            'display_error_details' => true,

            'log_errors' => true,

            'log_error_details' => true,
        ],
    ],
];
