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
];
