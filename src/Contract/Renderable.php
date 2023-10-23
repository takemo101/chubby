<?php

namespace Takemo101\Chubby\Contract;

/**
 * Renderable interface.
 */
interface Renderable
{
    /**
     * Convert the object to its string representation.
     *
     * @return string
     */
    public function render(): string;
}
