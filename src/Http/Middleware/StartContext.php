<?php

namespace Takemo101\Chubby\Http\Middleware;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\Http\Context\RequestContext;
use Takemo101\Chubby\Http\Event\BeforeStartContext;

/**
 * Start the request context.
 */
class StartContext implements MiddlewareInterface
{
    /**
     * constructor
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        private EventDispatcherInterface $dispatcher,
    ) {
        //
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = new RequestContext();
        $request = $context->withRequest($request);

        $this->dispatcher->dispatch(
            new BeforeStartContext(
                request: $request,
                context: $context,
            ),
        );

        return $handler->handle($request);
    }
}
