<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ErrorResponseRenders
{
    /**
     * @var ErrorResponseRender[]
     */
    private array $renders;

    /**
     * constructor
     *
     * @param ErrorResponseRender ...$renders
     */
    public function __construct(
        ErrorResponseRender ...$renders,
    ) {
        $this->setRender(...$renders);
    }

    /**
     * Add ErrorResponseRender.
     *
     * @param ErrorResponseRender ...$renders
     * @return self
     */
    public function addRender(ErrorResponseRender ...$renders): self
    {
        $this->renders = [
            ...$this->renders,
            ...$renders,
        ];

        return $this;
    }

    /**
     * Set ErrorResponseRender.
     *
     * @param ErrorResponseRender ...$renders
     * @return static
     */
    public function setRender(ErrorResponseRender ...$renders): static
    {
        $this->renders = empty($renders)
            ? [
                new HtmlErrorResponseRender(),
                new JsonErrorResponseRender(),
            ]
            : $renders;

        return $this;
    }

    /**
     * Perform response writing process.
     * Returns null if there is no response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return ResponseInterface
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Throwable $exception,
        ErrorSetting $setting,
    ): ResponseInterface {
        foreach ($this->renders as $render) {
            if ($output = $render->render($request, $response, $exception, $setting)) {
                return $output;
            }
        }

        return $response;
    }
}
