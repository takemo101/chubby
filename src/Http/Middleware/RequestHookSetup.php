<?php

namespace Takemo101\Chubby\Http\Middleware;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Event\RequestReceived;
use Takemo101\Chubby\Http\Event\ResponseSending;

/**
 * Middleware to hook into the processing of a request at the start.
 *
 * Acts as a gateway to hook into the request information after it starts
 * and the response information before it ends.
 */
class RequestHookSetup implements MiddlewareInterface
{
    /**
     * constructor
     *
     * @param EventDispatcherInterface $dispatcher
     * @param Hook $hook
     */
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private Hook $hook,
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
        /**
         * Hook into the request before it is processed
         *
         * @var ServerRequestInterface $request
         */
        $request = $this->hook->do(
            tag: ServerRequestInterface::class,
            parameter: $request,
        );

        /** @var ServerRequestInterface $request */
        $request = $this->hook->do(
            tag: ApplicationHookTags::Http_RequestReceived,
            parameter: $request,
        );

        // Dispatch an event indicating that an external request has been received
        $this->dispatcher->dispatch(
            new RequestReceived($request),
        );

        $response = $handler->handle($request);

        /**
         * Hook into the response before it is sent back to the client
         *
         * @var ResponseInterface $response
         */
        $response = $this->hook->do(
            tag: ResponseInterface::class,
            parameter: $response,
        );

        /** @var ResponseInterface $response */
        $response = $this->hook->do(
            tag: ApplicationHookTags::Http_ResponseSending,
            parameter: $response,
        );

        // Dispatch an event indicating the response is being sent back to the client
        $this->dispatcher->dispatch(
            new ResponseSending($response),
        );

        return $response;
    }
}
