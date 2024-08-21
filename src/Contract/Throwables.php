<?php

namespace Takemo101\Chubby\Contract;

use Throwable;

interface Throwables
{
    /**
     * Get an array of throwable objects.
     *
     * @return Throwable[]
     */
    public function getThrowables(): array;
}
