<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use LogicException;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Support\ApplicationPath;
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
     * @var string[] Dependency definitions paths.
     */
    private array $dependencyPaths = [];

    /**
     * constructor
     *
     * @param ApplicationPath $path
     * @param LocalFilesystem $filesystem
     */
    public function __construct(
        private readonly ApplicationPath $path,
        private readonly LocalFilesystem $filesystem,
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
        $dependencyPaths = $this->getDependencyPaths();

        /** @var mixed[] */
        $dependencies = [];

        foreach ($dependencyPaths as $dependencyPath) {
            /** @var mixed[] */
            $dependency = $this->filesystem->exists($dependencyPath)
                ? $this->filesystem->require($dependencyPath)
                : [];

            if (!is_array($dependency)) {
                throw new LogicException("Dependency definition must be array. ({$dependencyPath})");
            }

            $dependencies = [
                ...$dependencies,
                ...$dependency,
            ];
        }

        if (!empty($dependencies)) {
            $definitions->add(
                $dependencies,
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
     * Get dependency definitions paths.
     *
     * @return string[]
     */
    public function getDependencyPaths(): array
    {
        return $this->dependencyPaths ?: [$this->getDefaultDependencyPath()];
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
     * Set dependency definitions paths.
     *
     * @param string ...$paths
     * @return self
     */
    public function setDependencyPath(string ...$paths): self
    {
        $this->dependencyPaths = $paths;

        return $this;
    }
}
