<?php

namespace Takemo101\Chubby\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Takemo101\Chubby\Event\Concern\HasStoppable;

/**
 * Stoppable event.
 */
abstract class StoppableEvent implements StoppableEventInterface
{
    use HasStoppable;
}
