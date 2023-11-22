<?php

namespace Takemo101\Chubby\Filesystem\Mime;

use SplFileInfo;
use finfo;
use InvalidArgumentException;
use RuntimeException;

/**
 * @implements MimeTypeGuesser<SplFileInfo>
 *
 * reference: https://github.com/symfony/http-foundation
 */
class FinfoMimeTypeGuesser implements MimeTypeGuesser
{
    /**
     * Guess MimeType.
     * Returns null if it cannot be guessed.
     *
     * @param SplFileInfo $data
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function guess($data): ?string
    {
        if (!($data instanceof SplFileInfo)) {
            throw new InvalidArgumentException('This guesser only supports SplFileInfo.');
        }

        if (!$data->isFile() || !$data->isReadable()) {
            throw new InvalidArgumentException(sprintf('The "%s" file does not exist or is not readable.', $data->getPathname()));
        }

        if (!class_exists(finfo::class)) {
            throw new RuntimeException('PHP needs to be compiled with FileInfo support to use this component.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $mimeType = $finfo->file($data->getPathname());

        if ($mimeType && 0 === (\strlen($mimeType) % 2)) {
            $mimeStart = substr($mimeType, 0, \strlen($mimeType) >> 1);
            $mimeType = $mimeStart . $mimeStart === $mimeType ? $mimeStart : $mimeType;
        }

        return $mimeType ?: null;
    }
}
