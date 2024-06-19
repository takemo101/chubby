<?php

namespace Takemo101\Chubby\Http\Configurer;

use DI\Attribute\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Slim\App as Slim;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Event\AfterAddRoutingMiddleware;
use Takemo101\Chubby\Http\Event\AfterSlimConfiguration;
use Takemo101\Chubby\Http\Event\BeforeAddRoutingMiddleware;
use Takemo101\Chubby\Http\Event\BeforeSlimConfiguration;
use Takemo101\Chubby\Http\GlobalMiddlewareCollection;
use Takemo101\Chubby\Http\Middleware\StartContext;

class DefaultSlimConfigurer implements SlimConfigurer
{
    /**
     * constructor
     *
     * @param GlobalMiddlewareCollection $middlewares
     * @param EventDispatcherInterface $dispatcher
     * @param Hook $hook
     * @param string|null $basePath
     */
    public function __construct(
        private readonly GlobalMiddlewareCollection $middlewares,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly Hook $hook,
        #[Inject('config.slim.base_path')]
        private readonly ?string $basePath = null,
    ) {
        //
    }

    /**
     * Configure Slim application settings.
     *
     * @param Slim $slim
     * @return Slim
     */
    public function configure(Slim $slim): Slim
    {
        // Hook before slim configuration.
        $this->hook->do(
            tag: ApplicationHookTags::Http_BeforeSlimConfiguration,
            parameter: $slim,
        );

        // Dispatch event before slim configured.
        $this->dispatcher->dispatch(
            new BeforeSlimConfiguration($slim),
        );

        // Add base middlewares.
        $this->addBaseMiddlewares($slim);

        // Hook after slim configuration.
        $this->hook->do(
            tag: ApplicationHookTags::Http_AfterSlimConfiguration,
            parameter: $slim,
        );

        // Dispatch event after slim configured.
        $this->dispatcher->dispatch(
            new AfterSlimConfiguration($slim),
        );

        return $slim;
    }

    /**
     * Add basic middlewares.
     *
     * @param Slim $slim
     * @return void
     */
    private function addBaseMiddlewares(Slim $slim): void
    {
        // Add global middlewares.
        foreach ($this->middlewares->classes() as $middleware) {
            $slim->add($middleware);
        }

        if ($this->basePath) {
            $slim->setBasePath($this->basePath);
        }

        // Hook before add routing middleware.
        $this->hook->do(
            tag: ApplicationHookTags::Http_BeforeAddRoutingMiddleware,
            parameter: $slim,
        );

        // Dispatch event before add routing middleware.
        $this->dispatcher->dispatch(
            new BeforeAddRoutingMiddleware($slim),
        );

        $slim->addRoutingMiddleware();

        // Hook after add routing middleware.
        $this->hook->do(
            tag: ApplicationHookTags::Http_AfterAddRoutingMiddleware,
            parameter: $slim,
        );

        // Dispatch event after add routing middleware.
        $this->dispatcher->dispatch(
            new AfterAddRoutingMiddleware($slim),
        );

        $slim->add(BodyParsingMiddleware::class);
        $slim->add(ErrorMiddleware::class);
        $slim->add(StartContext::class);
    }
}
