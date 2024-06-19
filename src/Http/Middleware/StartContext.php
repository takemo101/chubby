<?php

namespace Takemo101\Chubby\Http\Middleware;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Context\ContextRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Context\RequestContext;
use Takemo101\Chubby\Http\Event\ContextCleared;
use Takemo101\Chubby\Http\Event\ContextCreated;

/**
 * Start the request context.
 */
class StartContext implements MiddlewareInterface
{
    /**
     * constructor
     *
     * @param ContextRepository $repository
     * @param EventDispatcherInterface $dispatcher
     * @param Hook $hook
     */
    public function __construct(
        private ContextRepository $repository,
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
        $context = new RequestContext();
        $request = $context->withRequest($request);

        // Set the context.
        $this->repository->set($context);

        $this->hook->doTyped($context);

        $this->hook->do(
            tag: ApplicationHookTags::Http_CreatedRequestContext,
            parameter: $context,
        );

        $this->dispatcher->dispatch(
            new ContextCreated(
                request: $request,
                context: $context,
            ),
        );

        $response = $handler->handle($request);

        // Clear the context.
        $this->repository->clear();

        $this->hook->do(
            tag: ApplicationHookTags::Http_ClearedRequestContext,
            parameter: $context,
        );

        $this->dispatcher->dispatch(
            new ContextCleared(
                response: $response,
                context: $context,
            ),
        );

        return $response;
    }
}
