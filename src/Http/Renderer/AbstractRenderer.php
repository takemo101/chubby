<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractRenderer implements ResponseRenderer
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
    final public function __construct(
        private mixed $data,
        private int $status = StatusCodeInterface::STATUS_OK,
        private array $headers = []
    ) {
        //
    }

    /**
     * Set data to be rendered.
     *
     * @param mixed $data
     * @return static
     */
    public function setData(mixed $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set status code to be rendered.
     *
     * @param int $status
     * @return static
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set headers to be rendered.
     *
     * @param array<string,string> $headers
     * @return static
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
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
            ->withHeader('Content-Type', $this->getContentType());

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $response->getBody()->write(
            $this->getContent($this->data),
        );

        return $response;
    }

    /**
     * Get response content type.
     *
     * @return string
     */
    protected function getContentType(): string
    {
        return static::ContentType;
    }

    /**
     * Get response body content to be rendered.
     *
     * @param mixed $data
     * @return string
     */
    abstract protected function getContent(mixed $data): string;
}
