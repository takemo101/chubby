<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigPhpRepository;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Filesystem\LocalSystem;
use Takemo101\Chubby\Support\ApplicationPath;

/**
 * Config data related.
 */
class ConfigProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'config';

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
                ConfigRepository::class => function (
                    LocalSystem $filesystem,
                    ApplicationPath $path,
                ): ConfigRepository {
                    return new ConfigPhpRepository(
                        filesystem: $filesystem,
                        directory: $path->getConfigPath(),
                    );
                },
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
        /** @var ConfigRepository */
        $config = $container->get(ConfigRepository::class);

        date_default_timezone_set($config->get('app.timezone', 'UTC'));
    }
}
