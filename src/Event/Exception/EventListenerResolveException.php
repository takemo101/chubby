<?php

namespace Takemo101\Chubby\Event\Exception;

use LogicException;

class EventListenerResolveException extends LogicException
{
    /**
     * @param class-string $class
     * @return self
     */
    public static function notCallableOrObjectError(string $class): self
    {
        return new self(
            sprintf(
                'The listener %s is not a callable or object.',
                $class,
            ),
        );
    }
}
