<?php

namespace Takemo101\Chubby\Http\Context;

use Exception;

class RequestContextException extends Exception
{
    /**
     * The error code indicating that the context is not an instance of the given class.
     */
    public const NotInstanceOfErrorCode = 1;

    /**
     * The error code indicating that the context is not found.
     */
    public const NotFoundErrorCode = 2;


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
            message: sprintf(
                'Context is not instance of %s. key: %s',
                $class,
                $key,
            ),
            code: self::NotInstanceOfErrorCode,
        );
    }

    /**
     * Creates a new instance of ContextException with a message indicating that the context is not found.
     *
     * @param string $key The key of the context.
     * @return self
     */
    public static function notFound(string $key): self
    {
        return new self(
            message: sprintf(
                'Context not found. key: %s',
                $key,
            ),
            code: self::NotFoundErrorCode,
        );
    }
}
