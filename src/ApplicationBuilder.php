<?php

namespace Takemo101\Chubby;

use Takemo101\Chubby\Bootstrap\Provider\ConsoleProvider;
use Takemo101\Chubby\Bootstrap\Provider\DependencyProvider;
use Takemo101\Chubby\Bootstrap\Provider\FunctionProvider;
use Takemo101\Chubby\Bootstrap\Provider\HttpProvider;

/**
 * Support classes for building applications.
 */
class ApplicationBuilder
{
    /**
     * constructor
     *
     * @param Application $app
     * @throws ApplicationAlreadyBootedException
     */
    final public function __construct(
        private Application $app,
    ) {
        if ($app->isBooted()) {
            throw new ApplicationAlreadyBootedException();
        }
    }

    /**
     * Add the ability to set dependencies loaded from setting/dependency.php in the DI container.
     *
     * @return static
     */
    public function addDependencySetting(): static
    {
        $this->getApplication()->addProvider(
            new DependencyProvider(
                $this->getApplication()->getPath(),
                $this->getApplication()->getFilesystem(),
            ),
        );

        return $this;
    }

    /**
     * Load setting/function.php so that you can initialize the application.
     *
     * @return static
     */
    public function addFunctionSetting(): static
    {
        $this->getApplication()->addProvider(
            new FunctionProvider(),
        );

        return $this;
    }

    /**
     * Add cli functionality using symfony/console.
     *
     * @return static
     */
    public function addConsole(): static
    {
        $this->getApplication()->addProvider(
            new ConsoleProvider(),
        );

        return $this;
    }

    /**
     * Add http routing function by slim.
     *
     * @return static
     */
    public function addHttp(): static
    {
        $this
            ->getApplication()
            ->addProvider(
                new HttpProvider(),
            );

        return $this;
    }

    /**
     * Obtain an Application instance with various functions given by ApplicationBuilder.
     *
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->app;
    }

    /**
     * Create ApplicationBuilder from any option.
     *
     * @param ApplicationOption|null $option
     * @return static
     */
    public static function fromOption(
        ?ApplicationOption $option = null,
    ): static {
        return new static(
            Application::fromOption(
                $option ?? ApplicationOption::from(),
            ),
        );
    }

    /**
     * Create ApplicationBuilder from any option and get Application as is.
     *
     * @param ApplicationOption|null $option
     * @return Application
     */
    public static function build(
        ?ApplicationOption $option = null,
    ): Application {
        return static::fromOption($option)
            ->getApplication();
    }

    /**
     * Create ApplicationBuilder from any option and get Application with standard settings.
     * It is recommended to create an instance of Application from this method in order to take advantage of all features.
     *
     * @param ApplicationOption|null $option
     * @return Application
     */
    public static function buildStandard(
        ?ApplicationOption $option = null,
    ): Application {
        return static::fromOption($option)
            ->addDependencySetting()
            ->addFunctionSetting()
            ->addConsole()
            ->addHttp()
            ->getApplication();
    }
}
