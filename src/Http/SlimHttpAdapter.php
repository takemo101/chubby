<?php

namespace Takemo101\Chubby\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App as Slim;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Takemo101\Chubby\Http\Concern\HasRouteCollectorProxy;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;

class SlimHttpAdapter implements RequestHandlerInterface, RouteCollectorProxyInterface
{
    use HasRouteCollectorProxy;

    /**
     * @var boolean
     */
    private bool $isConfigured = false;

    /**
     * constructor
     *
     * @param Slim $application
     * @param SlimConfigurer $configurer
     */
    public function __construct(
        private readonly Slim $application,
        private readonly SlimConfigurer $configurer,
    ) {
        //
    }

    /**
     * Configure slim application.
     *
     * @return void
     */
    private function configure(): void
    {
        if ($this->isConfigured) {
            return;
        }

        $this->configurer->configure($this->application);

        $this->isConfigured = true;
    }

    /**
     * @return boolean
     */
    public function isConfigured(): bool
    {
        return $this->isConfigured;
    }

    /**
     * Run slim application.
     *
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function run(?ServerRequestInterface $request = null): void
    {
        $this->configure();

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
        $this->configure();

        return $this->application->handle($request);
    }
}
