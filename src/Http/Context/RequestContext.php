<?php

namespace Takemo101\Chubby\Http\Context;

use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Context\Context;

/**
 * Getting and setting values of request-specific context.
 */
class RequestContext extends Context
{
    /**
     * The key of the request context.
     */
    public const ContextKey = 'request-context';

    /**
     * Sets the context for the request.
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function withRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->getAttribute(self::ContextKey) !== null) {
            return $request;
        }

        return $request->withAttribute(
            self::ContextKey,
            $this,
        );
    }

    /**
     * Creates a new request context from the specified request.
     *
     * @param ServerRequestInterface $request
     * @return self
     * @throws RequestContextException If the request context is not set.
     */
    public static function fromRequest(ServerRequestInterface $request): self
    {
        $context = $request->getAttribute(self::ContextKey);

        if ($context === null) {
            throw RequestContextException::notFound(self::ContextKey);
        }

        if ($context instanceof self) {
            return $context;
        }

        throw RequestContextException::notInstanceOf(
            self::ContextKey,
            self::class,
        );
    }
}
