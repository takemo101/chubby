<?php

namespace Takemo101\Chubby\Http\Support;

use Exception;

final class ContextException extends Exception
{
    /**
     * Creates a new instance of ContextException with a message indicating that the context is not found in the request.
     *
     * @param string $key The key of the context that was not found.
     * @return self
     */
    public static function notFound(string $key): self
    {
        return new self(
            sprintf(
                'Context is not found in request. key: %s',
                $key,
            ),
        );
    }

    /**
     * Creates a new instance of ContextException with a message indicating that the context is not an instance of the given class.
     *
     * @param string $key The key of the context.
     * @param string $class The name of the expected class.
     * @return self
     */
    public static function notInstanceOf(string $key, string $class): self
    {
        return new self(
            sprintf(
                'Context is not instance of %s. key: %s',
                $class,
                $key,
            ),
        );
    }
}
