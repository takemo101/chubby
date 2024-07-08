<?php

// Slim framework configuration

return [

    // Base path
    'base_path' => env('BASE_PATH'),

    // Error output settings
    'error' => [

        // ErrorMiddleware error display setting
        'setting' => [

            'display_error_details' => true,

            'log_errors' => true,

            'log_error_details' => true,
        ],
    ],

    // Global middleware settings
    'middlewares' => [
        // class-string<MiddlewareInterface>
    ],

    // This configuration is used to modify the dependencies for Slim.
    'dependencies' => [
        // Takemo101\Chubby\Http\Factory\SlimFactory::class => ImplSlimFactory::class,
        // Takemo101\Chubby\Http\Configurer\SlimConfigurer::class => ImplSlimConfigurer::class,
        // Slim\Interfaces\ErrorHandlerInterface::class => ImplErrorHandler::class,
    ],
];
