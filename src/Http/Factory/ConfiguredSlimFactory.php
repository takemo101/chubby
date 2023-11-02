<?php

namespace Takemo101\Chubby\Http\Factory;

use Slim\App as Slim;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;

final readonly class ConfiguredSlimFactory implements SlimFactory
{
    /**
     * constructor
     *
     * @param SlimFactory $factory
     * @param SlimConfigurer $configurer
     */
    public function __construct(
        private SlimFactory $factory,
        private SlimConfigurer $configurer,
    ) {
        //
    }

    /**
     * Create Slim application.
     *
     * @return Slim
     */
    public function create(): Slim
    {
        $app = $this->factory->create();

        return $this->configurer->configure($app);
    }
}
