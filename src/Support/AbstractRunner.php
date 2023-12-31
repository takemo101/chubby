<?php

namespace Takemo101\Chubby\Support;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Provider\Provider;

/**
 * Abstract class for running applications.
 */
abstract class AbstractRunner
{
    /**
     * constructor
     *
     * @param Application $app
     */
    final public function __construct(
        private Application $app,
    ) {
        //
    }

    /**
     * Add provider class instance.
     *
     * @return self
     */
    public function addProvider(Provider ...$providers): self
    {
        $this->app->addProvider(...$providers);

        return $this;
    }

    /**
     * Get application.
     *
     * @return Application
     */
    protected function getApp(): Application
    {
        return $this->app;
    }
}
