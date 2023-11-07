<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Takemo101\Chubby\Http\Renderer\JsonRenderer;

use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Throwable;

class JsonErrorResponseRender extends AbstractErrorResponseRender
{
    /**
     * Determine if the response should be rendered.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    protected function shouldRender(
        ServerRequestInterface $request,
    ): bool {
        return true;
    }

    /**
     * Create error response renderer.
     *
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return ResponseRenderer
     */
    protected function createRenderer(
        ServerRequestInterface $request,
        Throwable $exception,
        ErrorSetting $setting,
    ): ResponseRenderer {
        return new JsonRenderer(
            [
                'error' => $this->getErrorDetails($exception, $setting),
            ],
        );
    }

    /**
     * Get error message.
     *
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return array<string,mixed> Json error message
     */
    private function getErrorDetails(Throwable $exception, ErrorSetting $setting): array
    {
        return $setting->displayErrorDetails
            ? [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'previous' => $exception->getPrevious(),
                'trace' => $exception->getTrace(),
            ]
            : [
                'message' => $exception->getMessage(),
            ];
    }
}
