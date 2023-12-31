<?php

namespace Takemo101\Chubby\Http\Bridge;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Event\StoppableEvent;

/**
 * This is an event after running the controller.
 */
class AfterControllerInvoke extends StoppableEvent
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
