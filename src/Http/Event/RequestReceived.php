<?php

namespace Takemo101\Chubby\Http\Event;

use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Event\StoppableEvent;

/**
 * This is an event before receiving the request.
 */
class RequestReceived extends StoppableEvent
{
    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(
        private readonly ServerRequestInterface $request
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
