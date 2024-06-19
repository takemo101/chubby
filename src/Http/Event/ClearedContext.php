<?php

namespace Takemo101\Chubby\Http\Event;

use Psr\Http\Message\ResponseInterface;
use Takemo101\Chubby\Event\StoppableEvent;
use Takemo101\Chubby\Http\Context\RequestContext;

/**
 * This is an event after clearing the context.
 */
class ClearedContext extends StoppableEvent
{
    /**
     * constructor
     *
     * @param ResponseInterface $response
     * @param RequestContext $context
     */
    public function __construct(
        private ResponseInterface $response,
        private RequestContext $context,
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

    /**
     * Get the context.
     *
     * @return RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }
}
