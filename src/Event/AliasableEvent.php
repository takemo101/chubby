<?php

namespace Takemo101\Chubby\Event;

/**
 * @template T
 */
interface AliasableEvent
{
    /**
     * Get the event alias.
     *
     * @return class-string<T>
     */
    public function getAlias(): string;
}
