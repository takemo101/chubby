<?php

namespace Takemo101\Chubby\Http\Factory;

use Slim\App as Slim;

interface SlimFactory
{
    /**
     * Create Slim application.
     *
     * @return Slim
     */
    public function create(): Slim;
}
