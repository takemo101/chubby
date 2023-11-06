<?php

namespace Takemo101\Chubby\Filesystem\Mime;

/**
 * @template T
 */
interface MimeTypeGuesser
{
    /**
     * Guess MimeType.
     * Returns null if it cannot be guessed.
     *
     * @param T $data
     * @return string|null
     */
    public function guess($data): ?string;
}
