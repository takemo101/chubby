<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Throwable;

abstract class AbstractErrorResponseRender implements ErrorResponseRender
{
    /**
     * Perform response writing process.
     * Returns null if there is no response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return ResponseInterface|null
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Throwable $exception,
        ErrorSetting $setting,
    ): ?ResponseInterface {

        if (!$this->shouldRender($request)) {
            return null;
        }

        return $this->createRenderer(
            $request,
            $exception,
            $setting,
        )->render(
            $request,
            $response,
        );
    }

    /**
     * Determine if the response should be rendered.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    abstract protected function shouldRender(
        ServerRequestInterface $request,
    ): bool;

    /**
     * Create error response renderer.
     *
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return ResponseRenderer
     */
    abstract protected function createRenderer(
        ServerRequestInterface $request,
        Throwable $exception,
        ErrorSetting $setting,
    ): ResponseRenderer;
}
