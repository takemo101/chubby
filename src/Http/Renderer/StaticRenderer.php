<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use SplFileInfo;
use Takemo101\Chubby\Filesystem\Mime\FinfoMimeTypeGuesser;

class StaticRenderer extends AbstractStreamRenderer
{
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
            $guesser = new FinfoMimeTypeGuesser();

            if ($guessMimeType = $guesser->guess($data)) {
                return $guessMimeType;
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
     * @return static
     */
    public static function fromPath(
        string $path,
        string $mime = '',
        int $status = StatusCodeInterface::STATUS_OK,
        array $headers = []
    ): static {
        $finfo = new SplFileInfo($path);

        return new static(
            $finfo,
            $mime,
            $status,
            $headers
        );
    }
}
