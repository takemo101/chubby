<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Server\MiddlewareInterface;
use Takemo101\Chubby\Support\ClassCollection;

/**
 * @extends ClassCollection<MiddlewareInterface>
 */
class GlobalMiddlewareCollection extends ClassCollection
{
    public const Type = MiddlewareInterface::class;
}
