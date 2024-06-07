<?php

namespace Takemo101\Chubby\Http\Bridge;

use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Event\StoppableEvent;

/**
 * This is an event before running the controller.
 */
class BeforeControllerExecution extends StoppableEvent
{
    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(
        private ServerRequestInterface $request,
    ) {
        //
    }

    /**
     * Get the request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
