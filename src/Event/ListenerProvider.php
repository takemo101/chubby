<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

interface ListenerProvider extends ListenerProviderInterface
{
    /**
     * Get the listeners by event name.
     *
     * @param class-string|string $eventName Event name or class name
     * @return iterable<callable>
     */
    public function getListeners(string $eventName): iterable;
}
