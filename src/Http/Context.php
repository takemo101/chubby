<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Request and response context.
 */
final readonly class Context
{
    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(
        public readonly ServerRequestInterface $request,
        public readonly ResponseInterface $response,
    ) {
        //
    }
}
