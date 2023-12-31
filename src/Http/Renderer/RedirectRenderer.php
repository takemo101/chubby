<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

class RedirectRenderer implements ResponseRenderer
{
    /**
     * constructor
     *
     * @param string $url
     * @param int $status
     * @param array<string,string> $headers
     */
    public function __construct(
        private string $url,
        private int $status = StatusCodeInterface::STATUS_FOUND,
        private array $headers = []
    ) {
        if ($url === '') {
            throw new InvalidArgumentException('The url is empty.');
        }
    }

    /**
     * Set url to be rendered.
     *
     * @param string $url
     * @return static
     */
    public function setUrl(string $url): static
    {
        $this->url = $url;

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
            ->withHeader('Location', $this->url);

        foreach ($this->headers as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        return $response;
    }
}
