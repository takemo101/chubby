<?php

namespace Takemo101\Chubby\Config;

use LogicException;

class ConfigException extends LogicException
{
    /**
     * @return self
     */
    public static function invalidDataTypeError(): self
    {
        return new self(
            'The data type must be an array.',
        );
    }
}
