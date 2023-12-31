<?php

namespace Takemo101\Chubby\Event;

/**
 * @template T of object
 */
interface EventListener
{
    /**
     * Process the event
     *
     * @param T $event
     * @return void
     */
    public function __invoke(object $event): void;
}
