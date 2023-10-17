<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Filesystem\LocalSystem;
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
        //
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        $functionPath = $this->getFunctionPath();

        if ($this->filesystem->exists($functionPath)) {
            require $functionPath;
        }
    }

    /**
     * Get function path.
     *
     * @return string
     */
    protected function getFunctionPath(): string
    {
        return $this->path->getSettingPath('function.php');
    }
}
