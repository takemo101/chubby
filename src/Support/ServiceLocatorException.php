<?php

namespace Takemo101\Chubby\Support;

use LogicException;

class ServiceLocatorException extends LogicException
{
    /**
     * @return self
     */
    public static function notInitializedError(): self
    {
        return new self(
            'The service locator is not initialized.',
        );
    }
}
