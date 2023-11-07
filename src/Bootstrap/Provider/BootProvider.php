<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Psr\Container\ContainerInterface;
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
                Hook::class => fn (ContainerInterface $container) => new Hook($container),
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
