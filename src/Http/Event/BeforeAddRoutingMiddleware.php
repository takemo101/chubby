<?php

namespace Takemo101\Chubby\Http\Event;

use Slim\App as Slim;
use Takemo101\Chubby\Event\StoppableEvent;

/**
 * This is an event before adding routing middleware.
 */
class BeforeAddRoutingMiddleware extends StoppableEvent
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
