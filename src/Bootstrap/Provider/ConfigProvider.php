<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use DI\Factory\RequestedEntry;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigPhpRepository;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Filesystem\LocalSystem;
use Takemo101\Chubby\Support\ApplicationPath;
use Illuminate\Support\Arr;

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
     * @var string
     */
    public const ConfigPrependKey = 'config';

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
                    $config = new ConfigPhpRepository(
                        filesystem: $filesystem,
                        directory: $path->getConfigPath(),
                    );

                    return $config;
                },
                self::ConfigPrependKey . '.*' => function (
                    ConfigRepository $config,
                    RequestedEntry $entry,
                ) {
                    $key = (string) preg_replace(
                        '/^' . self::ConfigPrependKey . '\./',
                        '',
                        $entry->getName(),
                    );

                    return $config->get($key);
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

        $this->setDefaultTimezone($config);
    }

    private function setDefaultTimezone(ConfigRepository $config): void
    {
        /** @var string */
        $timezone = $config->get('app.timezone', 'UTC');

        date_default_timezone_set($timezone);
    }
}
