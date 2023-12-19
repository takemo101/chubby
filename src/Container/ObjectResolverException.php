<?php

namespace Takemo101\Chubby\Container;

use Exception;

class ObjectResolverException extends Exception
{
    /**
     * @param string|null $message
     * @param int $code
     */
    public function __construct(
        ?string $message = null,
        int $code = 0,
    ) {
        parent::__construct(
            $message ?? 'Failed to resolve object.',
            $code,
        );
    }

    /**
     * @param class-string $id
     * @return self
     */
    public static function notInstantiable(string $id): self
    {
        return new self(
            sprintf(
                'Class %s is not instantiable.',
                $id,
            ),
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function notSupportedVariadicParameter(
        string $name,
    ): self {
        return new self(
            sprintf(
                'Variadic parameter is not supported. [%s]',
                $name,
            ),
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function notFoundParameterType(
        string $name,
    ): self {
        return new self(
            sprintf(
                'Parameter type not found. [%s]',
                $name,
            ),
        );
    }

    /**
     * @param string $name
     * @return self
     */
    public static function notSupportedBuiltInType(
        string $name,
    ): self {
        return new self(
            sprintf(
                'Built-in type is not supported. [%s]',
                $name,
            ),
        );
    }

    /**
     * @param string $id
     * @return self
     */
    public static function invalidContainerReturnType(
        string $id,
    ): self {
        throw new ObjectResolverException(
            sprintf(
                'Container must return object. [%s]',
                $id,
            ),
        );
    }
}
