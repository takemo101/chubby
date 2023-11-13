<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Support\ApplicationPath;
use RuntimeException;
use Takemo101\Chubby\Filesystem\LocalFilesystem;

/**
 * Dependency injection related.
 */
final class DependencyProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'dependency';

    /**
     * @var string dependency.php relative path
     */
    private string $dependencyPath = 'dependency.php';

    /**
     * constructor
     *
     * @param ApplicationPath $path
     * @param LocalFilesystem $filesystem
     */
    public function __construct(
        private ApplicationPath $path,
        private LocalFilesystem $filesystem,
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
        $dependencyPath = $this->getDependencySettingPath($this->path);

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
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        //
    }

    /**
     * Get dependency definitions path.
     *
     * @param ApplicationPath $path
     * @return string
     */
    private function getDependencySettingPath(
        ApplicationPath $path,
    ): string {
        return $path->getSettingPath($this->dependencyPath);
    }

    /**
     * Set dependency definitions path.
     *
     * @param string $path
     * @return void
     */
    public function setDependencyPath(string $path): void
    {
        $this->dependencyPath = $path;
    }
}
