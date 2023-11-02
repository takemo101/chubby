<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App as Slim;
use Takemo101\Chubby\Http\Concern\HasRouteCollectorProxy;

final class SlimHttpAdapter implements RequestHandlerInterface
{
    use HasRouteCollectorProxy;

    /**
     * constructor
     *
     * @param Slim $application
     */
    public function __construct(
        private readonly Slim $application,
    ) {
        //
    }

    /**
     * Run slim application.
     *
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function run(?ServerRequestInterface $request = null): void
    {
        $this->application->run($request);
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->application->handle($request);
    }
}
