<?php

namespace Takemo101\Chubby\Http\Renderer;

use Takemo101\Chubby\Contract\Renderable;

class StringRenderer extends AbstractRenderer
{
    /**
     * Get response body content to be rendered.
     *
     * @param mixed $data
     * @return string
     */
    protected function getContent(mixed $data): string
    {
        return $data instanceof Renderable
            ? $data->render()
            : (string) $data; // @phpstan-ignore-line
    }
}
