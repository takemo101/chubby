<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Contract\Arrayable;

final class JsonRenderer implements ResponseRenderer
{
    /**
     * constructor
     *
     * @param mixed $data
     * @param int $status
     * @param array<string,string> $headers
     */
    public function __construct(
        private mixed $data,
        private int $status = StatusCodeInterface::STATUS_OK,
        private array $headers = []
    ) {
        //
    }

    /**
     * Perform response writing process.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response The response
     * @return ResponseInterface The response
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response = $response
            ->withStatus($this->status)
            ->withHeader('Content-Type', 'application/json');

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $response->getBody()->write(
            (string)json_encode(
                $this->data instanceof Arrayable
                    ? $this->data->toArray()
                    : $this->data,
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );

        return $response;
    }
}
