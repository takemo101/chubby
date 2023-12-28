<?php

namespace Takemo101\Chubby\Filesystem\Mime;

use SplFileInfo;

/**
 * @implements MimeTypeGuesser<SplFileInfo|string>
 */
class MockMimeTypeGuesser implements MimeTypeGuesser
{
    /**
     * constructor
     *
     * @param string|null $defaultMimeType
     */
    public function __construct(
        private ?string $defaultMimeType = null,
    ) {
        //
    }

    /**
     * Guess MimeType.
     * Returns null if it cannot be guessed.
     *
     * @param SplFileInfo|string $data
     * @return string|null
     */
    public function guess($data): ?string
    {
        return $this->defaultMimeType;
    }
}
