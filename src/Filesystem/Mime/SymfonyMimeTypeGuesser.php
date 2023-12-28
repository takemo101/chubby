<?php

namespace Takemo101\Chubby\Filesystem\Mime;

use SplFileInfo;
use InvalidArgumentException;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;

/**
 * @implements MimeTypeGuesser<SplFileInfo|string>
 *
 * reference: https://github.com/symfony/http-foundation
 */
class SymfonyMimeTypeGuesser implements MimeTypeGuesser
{
    /**
     * @var MimeTypesInterface
     */
    private MimeTypesInterface $mimeTypes;

    /**
     * constructor
     *
     * @param MimeTypesInterface|null $mimeTypes
     */
    public function __construct(
        ?MimeTypesInterface $mimeTypes = null,
    ) {
        $this->mimeTypes = $mimeTypes ?? MimeTypes::getDefault();
    }

    /**
     * Guess MimeType.
     * Returns null if it cannot be guessed.
     *
     * @param SplFileInfo|string $data
     * @return string|null
     * @throws InvalidArgumentException
     */
    public function guess($data): ?string
    {
        if (is_string($data)) {
            $data = new SplFileInfo($data);
        }

        if (!($data instanceof SplFileInfo)) {
            throw new InvalidArgumentException('Argument must be a string or SplFileInfo');
        }

        $mimeTypes = $this->mimeTypes->getMimeTypes($data->getExtension());

        $mimeType = $mimeTypes[0] ?? $this->mimeTypes->guessMimeType($data->getPathname());

        return $mimeType;
    }
}
