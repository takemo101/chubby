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
class DependencyProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'dependency';

    /**
     * @var string Default dependency.php relative path
     */
    public const DefaultDependencySettingPath = 'dependency.php';

    /**
     * @var string|null Dependency definitions path.
     */
    private ?string $dependencyPath = null;

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
        $dependencyPath = $this->getDependencyPath();

        /** @var mixed[] */
        $dependency = $this->filesystem->exists($dependencyPath)
            ? $this->filesystem->require($dependencyPath)
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
     * @return string
     */
    public function getDependencyPath(): string
    {
        return $this->dependencyPath ?: $this->getDefaultDependencyPath();
    }

    /**
     * Get default dependency definitions path.
     *
     * @return string
     */
    private function getDefaultDependencyPath(): string
    {
        return $this->path->getSettingPath(self::DefaultDependencySettingPath);
    }

    /**
     * Set dependency definitions path.
     *
     * @param string|null $path
     * @return void
     */
    public function setDependencyPath(?string $path = null): void
    {
        $this->dependencyPath = $path;
    }
}
