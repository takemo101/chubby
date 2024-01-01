<?php

// Event configuration

use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Event\EventListenerProvider;

return [
    // EventDispatcherInterface implementation class name
    'dispatcher' => EventDispatcher::class,

    // ListenerProviderInterface implementation class name
    'provider' => EventListenerProvider::class,

    // Event listing class name array
    'listen' => [
        // class-string<object&callable>
    ]
];
