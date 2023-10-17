<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Support\ApplicationPath;
use RuntimeException;
use Takemo101\Chubby\Filesystem\LocalSystem;
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
     * @param LocalSystem $filesystem
     */
    public function __construct(
        protected ApplicationPath $path,
        protected LocalSystem $filesystem,
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
        $dependencyPath = $this->getDependencyPath();

        /** @var mixed[] */
        $dependency = $this->filesystem->exists($dependencyPath)
            ? require $dependencyPath
            : [];

        if (!is_array($dependency)) {
            throw new RuntimeException('Dependency definition must be array.');
        }

        if (!empty($dependency)) {
            $definitions->add(
                $dependency,
            );
        }
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
