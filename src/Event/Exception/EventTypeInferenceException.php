<?php

namespace Takemo101\Chubby\Event\Exception;

use LogicException;

class EventTypeInferenceException extends LogicException
{
    public const CodeNotExistsMethod = 1;
    public const CodeNotExistsParameter = 2;
    public const CodeNotExistsType = 3;
    public const CodeNotClassType = 4;
    public const CodeFailedToResolveUnionType = 5;

    /**
     * @param class-string $class
     * @param string $method
     * @return self
     */
    public static function notExistsMethodError(string $class, string $method): self
    {
        return new self(
            sprintf(
                'The method %s::%s() does not exist.',
                $class,
                $method,
            ),
            self::CodeNotExistsMethod,
        );
    }

    /**
     * @param string $class
     * @param string $method
     * @return self
     */
    public static function notExistsParameterError(string $class, string $method): self
    {
        return new self(
            sprintf(
                'The method %s::%s() must have at least one parameter.',
                $class,
                $method,
            ),
            self::CodeNotExistsParameter,
        );
    }

    /**
     * @param string $class
     * @param string $method
     * @return self
     */
    public static function notExistsTypeError(string $class, string $method): self
    {
        return new self(
            sprintf(
                'The method %s::%s() must have a type.',
                $class,
                $method,
            ),
            self::CodeNotExistsType,
        );
    }

    /**
     * @param string $type
     * @return self
     */
    public static function notClassTypeError(string $type): self
    {
        return new self(
            sprintf(
                'The type %s is not a class.',
                $type,
            ),
            self::CodeNotExistsType,
        );
    }

    /**
     * @return self
     */
    public static function failedToResolveUnionTypeError(): self
    {
        return new self(
            'Failed to resolve union type.',
            self::CodeFailedToResolveUnionType,
        );
    }
}
