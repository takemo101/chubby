<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Throwable;

final class HtmlErrorResponseRender implements ErrorResponseRender
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

        $accept = $request->getHeaderLine('Accept');

        if (!str_contains($accept, 'text/html')) {
            return null;
        }

        $response->getBody()->write(
            (new HtmlErrorRenderer())->__invoke(
                $exception,
                $setting->displayErrorDetails,
            ),
        );

        return $response;
    }
}
