<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;

/**
 * Service provider.
 */
interface Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'provider';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void;

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void;
}
