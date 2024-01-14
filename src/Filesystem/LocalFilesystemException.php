<?php

namespace Takemo101\Chubby\Filesystem;

use RuntimeException;

/**
 * Exception regarding LocalSystem.
 */
class LocalFilesystemException extends RuntimeException
{
    public const CodeNotFound = 1;
    public const CodeIOError = 2;

    /**
     * @param string $path
     * @return self
     */
    public static function notFound(string $path): self
    {
        return new self("File not found at path [{$path}].", self::CodeNotFound);
    }

    /**
     * @param string $path
     * @return self
     */
    public static function ioError(string $path): self
    {
        return new self("IO error occurred at path [{$path}].", self::CodeIOError);
    }
}
