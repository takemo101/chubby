<?php

namespace Takemo101\Chubby\Http\Routing;

use RuntimeException;
use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DomainRouteHandleException extends RuntimeException
{
    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = 'Cannot handle request.',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create a throw exception request handler.
     * This is used when a route is not found.
     *
     * @return RequestHandlerInterface
     */
    public static function createNeverRequestHandler(): RequestHandlerInterface
    {
        return new class () implements RequestHandlerInterface {
            /**
             * {@inheritDoc}
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw new DomainRouteHandleException();
            }
        };
    }
}
