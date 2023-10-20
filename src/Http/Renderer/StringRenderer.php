<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class StringRenderer implements ResponseRenderer
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
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response = $response
            ->withStatus($this->status)
            ->withHeader('Content-Type', 'text/plain');

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $response->getBody()->write(
            (string) $this->data, // @phpstan-ignore-line
        );

        return $response;
    }
}
