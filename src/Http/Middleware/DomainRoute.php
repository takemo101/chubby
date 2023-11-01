<?php

namespace Takemo101\Chubby\Http\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Takemo101\Chubby\Http\Routing\DomainRouteContext;
use Takemo101\Chubby\Http\Routing\DomainRouteDispatcher;

/**
 * Handles the next middleware when a domain route is found for the request.
 */
final class DomainRoute implements MiddlewareInterface
{
    /** @var string */
    public const CommonRequestMethod = '*';
    /**
     * constructor
     *
     * @param DomainRouteDispatcher $dispatcher
     */
    public function __construct(
        private DomainRouteDispatcher $dispatcher,
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
        $host = $request->getUri()->getHost();

        $result = $this->dispatcher->dispatch($host);

        if (!$result->isFound()) {
            throw new HttpNotFoundException($request);
        }

        $context = new DomainRouteContext($result->getArguments());

        return $handler->handle(
            $context->composeRequest($request),
        );
    }

    /**
     * Create a new instance.
     *
     * @param string $domain
     */
    public static function fromDomain(string $domain): self
    {
        return new self(new DomainRouteDispatcher($domain));
    }
}
