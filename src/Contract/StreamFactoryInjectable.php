<?php

namespace Takemo101\Chubby\Contract;

use Psr\Http\Message\StreamFactoryInterface;

interface StreamFactoryInjectable
{
    /**
     * Set the ssr17 stream factory implementation.
     *
     * @param StreamFactoryInterface $factory
     * @return void
     */
    public function setStreamFactory(StreamFactoryInterface $factory): void;
}
