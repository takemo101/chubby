<?php

namespace Takemo101\Chubby\Http\Event;

use Slim\App as Slim;
use Takemo101\Chubby\Event\StoppableEvent;

/**
 * This is an event after configuring the slim application.
 */
class ConfiguredSlim extends StoppableEvent
{
    /**
     * constructor
     *
     * @param Slim $slim
     */
    public function __construct(
        private Slim $slim
    ) {
        //
    }

    /**
     * Get slim application.
     *
     * @return Slim
     */
    public function getSlim(): Slim
    {
        return $this->slim;
    }
}
