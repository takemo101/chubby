<?php

namespace Takemo101\Chubby\Http\Context;

use Psr\Http\Message\ServerRequestInterface;

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
    public function withRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $context = RequestContext::fromRequest($request);

        $context->set(
            static::ContextKey,
            $this,
        );

        foreach ($this->getContextValues() as $id => $value) {
            $context->set($id, $value);
        }

        return $request;
    }

    /**
     * Get contextual data.
     *
     * @return array<string,mixed> The key-value pairs of the contextual data.
     */
    protected function getContextValues(): array
    {
        return [];
    }

    /**
     * Create a context instance from a ServerRequest
     *
     * @param ServerRequestInterface $request
     * @return static|null
     * @throws ContextException
     */
    public static function fromRequest(
        ServerRequestInterface $request,
    ): ?static {
        $context = RequestContext::fromRequest($request);

        /** @var static|null */
        $value = $context->get(static::ContextKey);

        if ($value === null) {
            return null;
        }

        if (!($value instanceof static)) {
            throw ContextException::notInstanceOf(
                static::ContextKey,
                static::class,
            );
        }

        return $value;
    }
}
