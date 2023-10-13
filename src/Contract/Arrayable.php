<?php

namespace Takemo101\Chubby\Contract;

/**
 * Arrayable interface.
 */
interface Arrayable
{
    /**
     * Convert the object to its array representation.
     *
     * @return mixed[]
     */
    public function toArray(): array;
}
