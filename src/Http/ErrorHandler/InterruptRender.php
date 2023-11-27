<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

/**
 * Exception class for rendering by throwing the exception.
 */
class InterruptRender extends Exception implements ResponseRenderer
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

    public static function fromResponse(
        ResponseInterface $response,
    ): self {
        return new self(
            // Create a renderer that returns the response using an unknown class
            new class ($response) implements ResponseRenderer {
                /**
                 * constructor
                 *
                 * @param ResponseInterface $response
                 */
                public function __construct(
                    private ResponseInterface $response,
                ) {
                    //
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
                    return $this->response;
                }
            },
        );
    }
}
