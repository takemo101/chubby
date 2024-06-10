<?php

namespace Takemo101\Chubby\Log;

use Monolog\Processor\ProcessorInterface;
use Takemo101\Chubby\Support\ClassCollection;

/**
 * @extends ClassCollection<ProcessorInterface>
 */
class LoggerProcessorCollection extends ClassCollection
{
    public const Type = ProcessorInterface::class;
}
