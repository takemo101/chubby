<?php

namespace Takemo101\Chubby\Event\Concern;

/**
 * Aliasable event trait.
 */
trait HasAliasable
{
    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return static::class;
    }
}
