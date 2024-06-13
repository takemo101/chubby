<?php

namespace Takemo101\Chubby\Bootstrap\Support;

use Exception;

/**
 * Exception for dependency support.
 */
class DependencySupportException extends Exception
{
    public const UnsupportedEntryClassErrorCode = 1;

    /**
     * Create an exception when the specified class does not implement the interface of the entry class.
     *
     * @param string $class
     * @param string $entryClass
     * @return self
     */
    public static function unsupportedEntryClass(
        string $class,
        string $entryClass,
    ): self {
        return new self(
            message: "Class [{$class}] does not implement the interface of the entry class [{$entryClass}].",
        );
    }
}
