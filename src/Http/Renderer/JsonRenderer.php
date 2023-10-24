<?php

namespace Takemo101\Chubby\Http\Renderer;

use Takemo101\Chubby\Contract\Arrayable;

final class JsonRenderer extends AbstractRenderer
{
    /** @var string */
    public const ContentType = 'application/json';

    /**
     * Get response body content to be rendered.
     *
     * @param mixed $data
     * @return string
     */
    protected function getContent(mixed $data): string
    {
        return (string)json_encode(
            $data instanceof Arrayable
                ? $data->toArray()
                : $data,
            JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
    }
}
