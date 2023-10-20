<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Support\ServiceLocator;

/**
 * Boot related.
 */
class BootProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'boot';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions->add(
            [
                Hook::class => fn () => new Hook(),
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        ServiceLocator::initialize($container);
    }
}
