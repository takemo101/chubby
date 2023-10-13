<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as Slim;
use Takemo101\Chubby\Support\AbstractRunner;

/**
 * Execute Http processing by Slim application.
 */
final readonly class Http extends AbstractRunner
{
    /**
     * Run slim application
     *
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function run(?ServerRequestInterface $request = null): void
    {
        $this->getApp()->boot();

        /** @var Slim */
        $slim = $this->getApp()->get(Slim::class);

        $slim->run($request);
    }

    /**
     * Handle a request
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->getApp()->boot();

        /** @var Slim */
        $slim = $this->getApp()->get(Slim::class);

        return $slim->handle($request);
    }
}
