<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Takemo101\Chubby\Http\Renderer\JsonRenderer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class JsonErrorResponseRender implements ErrorResponseRender
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
        return (new JsonRenderer(
            [
                'error' => $this->getErrorDetails($exception, $setting),
            ],
        ))->render($request, $response);
    }

    /**
     * Get error message.
     *
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return array Json error message
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
