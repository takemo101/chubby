<?php

namespace Takemo101\Chubby\Hook;

use LogicException;

class HookException extends LogicException
{
    public const CodeInvalidFunctionParameterCount = 1;
    public const CodeInvalidFunctionParameterType = 2;

    /**
     * @return self
     */
    public static function invalidFunctionParameterCount(): self
    {
        return new self(
            'The function must have at least one or two parameter.',
            self::CodeInvalidFunctionParameterCount,
        );
    }

    /**
     * @return self
     */
    public static function invalidFunctionParameterType(): self
    {
        return new self(
            'The function parameter must be a named type.',
            self::CodeInvalidFunctionParameterType,
        );
    }
}
