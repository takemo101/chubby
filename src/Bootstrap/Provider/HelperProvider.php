<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;

/**
 * Helper functions related.
 */
class HelperProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'helper';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        require __DIR__ . '/../../helper.php';
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
}
