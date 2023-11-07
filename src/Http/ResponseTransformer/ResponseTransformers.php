<?php

namespace Takemo101\Chubby\Http\ResponseTransformer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ResponseTransformers
{
    /**
     * @var ResponseTransformer[]
     */
    private array $transformers;

    /**
     * constructor
     *
     * @param ResponseTransformer ...$transformers
     */
    public function __construct(
        ResponseTransformer ...$transformers,
    ) {
        $this->transformers = $transformers;
    }

    /**
     * Add ResponseTransformer.
     *
     * @param ResponseTransformer ...$transformers
     * @return self
     */
    public function addTransformer(ResponseTransformer ...$transformers): self
    {
        $this->transformers = [
            ...$this->transformers,
            ...$transformers,
        ];

        return $this;
    }

    /**
     * Change the data type and convert it into a response object.
     * If not converted to a response, return null.
     *
     * @param mixed $data
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function transform(
        mixed $data,
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        if ($data instanceof ResponseInterface) {
            return $data;
        }

        foreach ($this->transformers as $transformer) {
            if ($output = $transformer->transform($data, $request, $response)) {
                return $output;
            }
        }

        return $response;
    }
}
