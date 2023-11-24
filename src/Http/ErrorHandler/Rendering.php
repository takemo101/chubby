<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Exception class for rendering.
 */
class Rendering extends Exception implements ResponseRenderer
{
    /**
     * constructor
     *
     * @param ResponseRenderer $renderer
     */
    public function __construct(
        private ResponseRenderer $renderer,
    ) {
        parent::__construct(
            message: 'This exception is used for rendering.',
        );
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
        return $this->renderer->render($request, $response);
    }
}
