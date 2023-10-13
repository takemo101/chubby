<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface ErrorResponseRender
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
    ): ?ResponseInterface;
}
