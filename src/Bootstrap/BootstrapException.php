<?php

namespace Takemo101\Chubby\Bootstrap;

use LogicException;

class BootstrapException extends LogicException
{
    /**
     * @param string $name
     * @return self
     */
    public static function alreadyRegisteredError(string $name): self
    {
        return new self(
            sprintf(
                'The provider %s is already registered.',
                $name,
            ),
        );
    }
}
