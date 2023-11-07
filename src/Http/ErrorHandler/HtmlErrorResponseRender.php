<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Takemo101\Chubby\Http\Renderer\HtmlRenderer;
use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Throwable;

class HtmlErrorResponseRender extends AbstractErrorResponseRender
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
        $accept = $request->getHeaderLine('Accept');

        return str_contains($accept, 'text/html');
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
        return new HtmlRenderer(
            (new HtmlErrorRenderer())->__invoke(
                $exception,
                $setting->displayErrorDetails,
            ),
        );
    }
}
