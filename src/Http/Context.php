<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Request and response context.
 */
class Context
{
    /**
     * @var array<string,string>
     */
    private array $routeArguments;

    /**
     * constructor
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string,string> $routeArguments
     */
    public function __construct(
        private ServerRequestInterface $request,
        private ResponseInterface $response,
        array $routeArguments = [],
    ) {
        $this->routeArguments = $routeArguments;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return array<string,string>
     */
    public function getRouteArguments(): array
    {
        return $this->routeArguments;
    }
}
