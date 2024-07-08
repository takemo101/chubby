<?php

// Event configuration

return [

    // Event listing class name array
    'listeners' => [
        // class-string<object&callable>
    ],

    // This configuration is used to modify the dependencies for Event.
    'dependencies' => [
        // Symfony\Contracts\EventDispatcher\EventDispatcherInterface::class  => ImplEventDispatcher::class,
        // Takemo101\Chubby\Event\ListenerProvider::class => EventListenerProvider::class,
    ],
];
