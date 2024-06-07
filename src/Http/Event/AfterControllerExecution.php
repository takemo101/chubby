<?php

namespace Takemo101\Chubby\Http\Event;

use Psr\Http\Message\ResponseInterface;
use Takemo101\Chubby\Event\StoppableEvent;

/**
 * This is an event after running the controller.
 */
class AfterControllerExecution extends StoppableEvent
{
    /**
     * constructor
     *
     * @param ResponseInterface $response
     */
    public function __construct(
        private ResponseInterface $response,
    ) {
        //
    }

    /**
     * Get the response.
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
