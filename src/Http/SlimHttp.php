<?php

namespace Takemo101\Chubby\Http;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App as Slim;
use Takemo101\Chubby\Http\Concern\HasRouting;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;
use Takemo101\Chubby\Http\Event\ConfiguredSlim;

class SlimHttp implements RequestHandlerInterface
{
    use HasRouting;

    /**
     * @var boolean
     */
    private bool $isConfigured = false;

    /**
     * constructor
     *
     * @param Slim $application
     * @param SlimConfigurer $configurer
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        private readonly Slim $application,
        private readonly SlimConfigurer $configurer,
        private readonly EventDispatcherInterface $dispatcher,
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

        // Dispatch event after slim configured.
        $this->dispatcher->dispatch(
            new ConfiguredSlim($this->application),
        );

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
