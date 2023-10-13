<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Support\ApplicationPath;
use RuntimeException;
use Takemo101\Chubby\Hook\Hook;

/**
 * Dependency injection related.
 */
class DependencyProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'dependency';

    /**
     * constructor
     *
     * @param ApplicationPath $path
     */
    public function __construct(
        protected ApplicationPath $path,
    ) {
        //
    }

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        /** @var mixed[] */
        $dependency = require $this->getDependencyPath();

        if (!is_array($dependency)) {
            throw new RuntimeException('Dependency definition must be array.');
        }

        $definitions->add(
            $dependency,
            [
                Hook::class => fn () => new Hook(),
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        //
    }

    /**
     * Get dependency definitions path.
     *
     * @return string
     */
    protected function getDependencyPath(): string
    {
        return $this->path->getSettingPath('dependency.php');
    }
}
