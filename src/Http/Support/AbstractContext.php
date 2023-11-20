<?php

namespace Takemo101\Chubby\Http\Support;

use Psr\Http\Message\ServerRequestInterface;
use Closure;

abstract class AbstractContext
{
    /** @var string */
    public const ContextKey = '__context__';

    /**
     * Get request with contextual data.
     *
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function withContext(ServerRequestInterface $request): ServerRequestInterface
    {
        $request = $request->withAttribute(
            static::ContextKey,
            $this,
        );

        foreach ($this->getServerRequestAttributes() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $request;
    }

    /**
     * Get contextual data.
     *
     * @return array<string,mixed>
     */
    protected function getServerRequestAttributes(): array
    {
        return [];
    }

    /**
     * Create a context instance from a ServerRequest
     *
     * @param ServerRequestInterface $request
     * @param null|Closure():static $factory A factory function that returns a new instance of the context.
     * @return static
     * @throws ContextException
     */
    public static function fromServerRequest(
        ServerRequestInterface $request,
        ?Closure $factory = null,
    ): static {
        /** @var static|null */
        $context = $request->getAttribute(static::ContextKey);

        if (is_null($context)) {
            if (is_null($factory)) {
                throw ContextException::notFound(static::ContextKey);
            }

            $context = $factory();
        }

        if (!($context instanceof static)) {
            throw ContextException::notInstanceOf(
                static::ContextKey,
                static::class,
            );
        }

        return $context;
    }
}
