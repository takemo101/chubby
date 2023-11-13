<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;

/**
 * Function related.
 */
final class FunctionProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'function';

    /**
     * @var string function.php relative path
     */
    private string $functionPath = 'function.php';

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
        /** @var ApplicationPath */
        $path = $container->get(ApplicationPath::class);

        /** @var LocalFilesystem */
        $filesystem = $container->get(LocalFilesystem::class);

        $functionPath = $this->getFunctionSettingPath($path);

        if ($filesystem->exists($functionPath)) {
            require $functionPath;
        }
    }

    /**
     * Get function path.
     *
     * @param ApplicationPath $path
     * @return string
     */
    private function getFunctionSettingPath(ApplicationPath $path): string
    {
        return $path->getSettingPath($this->functionPath);
    }

    /**
     * Set function path.
     *
     * @param string $path
     * @return void
     */
    public function setFunctionPath(string $path): void
    {
        $this->functionPath = $path;
    }
}
