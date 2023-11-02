<?php

namespace Takemo101\Chubby\Http\Routing;

use RuntimeException;
use Throwable;
use Closure;

final class DomainRouteHandleException extends RuntimeException
{
    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Cannot handle request.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create a closure that throws an exception.
     *
     * @return Closure
     */
    public static function createThrowHandler(): Closure
    {
        return fn () => throw new self();
    }
}
