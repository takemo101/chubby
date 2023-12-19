<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;

/**
 * Function related.
 */
class FunctionProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'function';

    /**
     * @var string Default function.php relative path
     */
    public const DefaultFunctionSettingPath = 'function.php';

    /**
     * @var string|null function.php relative path
     */
    private ?string $functionPath = null;

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
        //
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        $functionPath = $this->getFunctionPath();

        if ($this->filesystem->exists($functionPath)) {
            $this->filesystem->require($functionPath);
        }
    }

    /**
     * Get function path.
     *
     * @return string
     */
    public function getFunctionPath(): string
    {
        return $this->functionPath ?: $this->getDefaultFunctionPath();
    }

    /**
     * Get default function path.
     *
     * @return string
     */
    private function getDefaultFunctionPath(): string
    {
        return $this->path->getSettingPath(self::DefaultFunctionSettingPath);
    }

    /**
     * Set function path.
     *
     * @param string|null $path
     * @return void
     */
    public function setFunctionPath(?string $path = null): void
    {
        $this->functionPath = $path;
    }
}
