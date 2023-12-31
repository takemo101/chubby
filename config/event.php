<?php

// Application basic settings

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Event\EventListenerProvider;

return [
    // EventDispatcherInterface implementation class name
    'dispatcher' => EventDispatcher::class,

    // ListenerProviderInterface implementation class name
    'provider' => EventListenerProvider::class,

    // Events and listeners mapping
    // Event class name => [Listner class name, ...] or listener class name
    'listen' => [
        // class-string => class-string<EventListener>
    ]
];
