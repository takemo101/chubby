<?php

namespace Takemo101\Chubby\Contract;

/**
 * Arrayable interface.
 *
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable
{
    /**
     * Convert the object to its array representation.
     *
     * @return array<TKey,TValue>
     */
    public function toArray(): array;
}
