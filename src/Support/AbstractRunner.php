<?php

namespace Takemo101\Chubby\Support;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationOption;
use Takemo101\Chubby\Bootstrap\Provider\ConsoleProvider;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\Bootstrap\Provider\HttpProvider;

/**
 * Abstract class for running applications.
 */
abstract readonly class AbstractRunner
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

    /**
     * Create an application instance with standard functionality from any option.
     *
     * @param ApplicationOption|null $option
     * @return static
     */
    public static function create(
        ?ApplicationOption $option = null
    ): static {
        return new static(Application::create(
            $option ?? ApplicationOption::from(),
        )->addProvider(
            new HttpProvider(),
            new ConsoleProvider(),
        ));
    }

    /**
     * Create an application instance with simple functionality from any option.
     *
     * @param ApplicationOption|null $option
     * @return static
     */
    public static function createSimple(
        ?ApplicationOption $option = null
    ): static {
        return new static(Application::createSimple(
            $option ?? ApplicationOption::from(),
        )->addProvider(
            new HttpProvider(),
            new ConsoleProvider(),
        ));
    }
}
