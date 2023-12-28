<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Takemo101\Chubby\Filesystem\Mime\MimeTypeGuesser;
use SplFileInfo;
use LogicException;

class StaticRenderer extends AbstractStreamRenderer
{
    /**
     * Get the MimeTypeGuesser implementation.
     *
     * @return MimeTypeGuesser<SplFileInfo|string>
     * @throws LogicException
     */
    private function getMimeTypeGuesser(): MimeTypeGuesser
    {
        /** @var MimeTypeGuesser<SplFileInfo|string> */
        $guesser = $this->getContainer()->get(MimeTypeGuesser::class);

        return $guesser;
    }

    /**
     * Get content type to be rendered.
     *
     * @return string
     */
    protected function getContentType(): string
    {
        if ($mime = $this->getMimeType()) {
            return $mime;
        }

        $data = $this->getData();

        if ($data instanceof SplFileInfo) {
            if ($mimeType = $this->getMimeTypeGuesser()->guess($data)) {
                return $mimeType;
            }
        }

        return static::DefaultContentType;
    }

    /**
     * Create a renderer instance from a file path.
     *
     * @param string $path
     * @param string $mime
     * @param int $status
     * @param array<string,string> $headers
     * @return self
     */
    public static function fromPath(
        string $path,
        string $mime = '',
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = []
    ): self {
        $finfo = new SplFileInfo($path);

        return new self(
            $finfo,
            $mime,
            $status,
            $headers
        );
    }
}
