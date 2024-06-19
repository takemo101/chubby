<?php

namespace Takemo101\Chubby\Http\Event;

use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Event\StoppableEvent;
use Takemo101\Chubby\Http\Context\RequestContext;

/**
 * This is an event after creating the context.
 */
class ContextCreated extends StoppableEvent
{
    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     * @param RequestContext $context
     */
    public function __construct(
        private ServerRequestInterface $request,
        private RequestContext $context,
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
