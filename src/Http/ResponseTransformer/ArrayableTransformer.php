<?php

namespace Takemo101\Chubby\Http\ResponseTransformer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Contract\Arrayable;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;

final class ArrayableTransformer implements ResponseTransformer
{
    /**
     * Change the data type and convert it into a response object.
     * If not converted to a response, return null.
     *
     * @param mixed $data
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface|null
     */
    public function transform(
        mixed $data,
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ?ResponseInterface {
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        return is_array($data)
            ? (new JsonRenderer($data))->render($request, $response)
            : null;
    }
}
