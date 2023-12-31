<?php

namespace Takemo101\Chubby\Filesystem;

use RuntimeException;

/**
 * Exception regarding LocalSystem.
 */
class LocalFilesystemException extends RuntimeException
{
    /**
     * @param string $path
     * @return self
     */
    public static function notFound(string $path): self
    {
        return new self("File not found at path [{$path}].");
    }

    /**
     * @param string $path
     * @return self
     */
    public static function ioError(string $path): self
    {
        return new self("IO error occurred at path [{$path}].");
    }
}
