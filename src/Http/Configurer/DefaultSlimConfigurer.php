<?php

namespace Takemo101\Chubby\Http\Configurer;

use DI\Attribute\Inject;
use Slim\App as Slim;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Middleware\StartContext;

class DefaultSlimConfigurer implements SlimConfigurer
{
    /**
     * @var string|null
     */
    private ?string $basePath;

    /**
     * constructor
     *
     * @param EventDispatcher $dispatcher
     * @param Hook $hook
     * @param string|null $basePath
     */
    public function __construct(
        private readonly EventDispatcher $dispatcher,
        private readonly Hook $hook,
        #[Inject('config.slim.base_path')]
        ?string $basePath = null,
    ) {
        $this->basePath = $basePath;
    }

    /**
     * Configure Slim application settings.
     *
     * @param Slim $slim
     * @return Slim
     */
    public function configure(Slim $slim): Slim
    {
        $this->hook->do(
            tag: ApplicationHookTags::Http_BeforeSlimConfiguration,
            parameter: $slim,
        );

        if ($this->basePath) {
            $slim->setBasePath($this->basePath);
        }

        $this->hook->do(
            tag: ApplicationHookTags::Http_BeforeAddRoutingMiddleware,
            parameter: $slim,
        );

        $slim->addRoutingMiddleware();

        $this->hook->do(
            tag: ApplicationHookTags::Http_AfterAddRoutingMiddleware,
            parameter: $slim,
        );

        $slim->add(BodyParsingMiddleware::class);
        $slim->add(ErrorMiddleware::class);
        $slim->add(StartContext::class);

        $this->hook->do(
            tag: ApplicationHookTags::Http_AfterSlimConfiguration,
            parameter: $slim,
        );

        return $slim;
    }
}
