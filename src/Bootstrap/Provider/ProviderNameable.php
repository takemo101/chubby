<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

/**
 * Has an implementation that gets the name of the provider.
 */
interface ProviderNameable
{
    /**
     * Get provider name.
     *
     * @return string
     */
    public function getProviderName(): string;
}
