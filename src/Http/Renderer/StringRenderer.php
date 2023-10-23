<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Contract\Renderable;

class StringRenderer implements ResponseRenderer
{
    /** @var string */
    public const ContentType = 'text/plain';

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
            ->withHeader('Content-Type', static::ContentType);

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $data = $this->data;

        if ($data instanceof Renderable) {
            $data = $data->render();
        }

        $response->getBody()->write(
            (string) $data, // @phpstan-ignore-line
        );

        return $response;
    }
}
