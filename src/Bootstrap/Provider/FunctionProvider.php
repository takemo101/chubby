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

        $functionPath = $this->getFunctionPath($path);

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
    protected function getFunctionPath(ApplicationPath $path): string
    {
        return $path->getSettingPath('function.php');
    }
}
