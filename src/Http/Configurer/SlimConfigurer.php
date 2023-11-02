<?php

namespace Takemo101\Chubby\Http\Configurer;

use Slim\App as Slim;

interface SlimConfigurer
{
    /**
     * Configure Slim application settings.
     *
     * @param Slim $slim
     * @return Slim
     */
    public function configure(Slim $slim): Slim;
}
