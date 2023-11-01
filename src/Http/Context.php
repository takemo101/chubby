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
     * @var array<string,mixed>
     */
    public array $routeArguments;

    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string,mixed> $routeArguments
     */
    public function __construct(
        public ServerRequestInterface $request,
        public ResponseInterface $response,
        array $routeArguments = [],
    ) {
        $this->routeArguments = $routeArguments;
    }
}
