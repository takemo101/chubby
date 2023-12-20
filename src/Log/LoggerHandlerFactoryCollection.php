<?php

namespace Takemo101\Chubby\Log;

use Takemo101\Chubby\Support\ClassCollection;
use Takemo101\Chubby\Log\Factory\LoggerHandlerFactory;

/**
 * @extends ClassCollection<LoggerHandlerFactory>
 */
class LoggerHandlerFactoryCollection extends ClassCollection
{
    public const Type = LoggerHandlerFactory::class;
}
