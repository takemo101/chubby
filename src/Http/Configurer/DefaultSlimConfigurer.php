<?php

namespace Takemo101\Chubby\Http\Configurer;

use DI\Attribute\Inject;
use Slim\App as Slim;
use Slim\Middleware\ErrorMiddleware;

class DefaultSlimConfigurer implements SlimConfigurer
{
    /**
     * @var string|null
     */
    private ?string $basePath;

    /**
     * constructor
     *
     * @param string|null $basePath
     */
    public function __construct(
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
        if ($this->basePath) {
            $slim->setBasePath($this->basePath);
        }

        $slim->addBodyParsingMiddleware();
        $slim->addRoutingMiddleware();
        $slim->add(ErrorMiddleware::class);

        return $slim;
    }
}
