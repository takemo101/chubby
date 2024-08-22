<?php

namespace Takemo101\Chubby\Event\Exception;

use Takemo101\Chubby\Exception\Exceptions;
use Throwable;

/**
 * Exception for handling multiple exceptions that occurred in event listeners.
 */
class EventListenerHandlingExceptions extends Exceptions
{
    public const Message = 'Multiple exceptions occurred in event listeners';

    /**
     * Create a new instance if the array is not empty.
     *
     * @param Throwable ...$throwables The exceptions that occurred.
     * @return static|null
     */
    public static function createIfNotEmpty(Throwable ...$throwables): ?self
    {
        if (empty($throwables)) {
            return null;
        }

        return new static(...$throwables);
    }
}
