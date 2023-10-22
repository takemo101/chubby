<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

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
    public const ConfigPrependKey = 'config.';

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

        $this->setAllConfigToContainer($container, $config);
        $this->setDefaultTimezone($config);
    }

    /**
     * Set config data to container.
     *
     * @param ApplicationContainer $container
     * @param ConfigRepository $config
     * @return void
     */
    private function setAllConfigToContainer(
        ApplicationContainer $container,
        ConfigRepository $config,
    ): void {
        $allConfig = $config->all();

        if (!empty($allConfig)) {
            $dotAllConfig = Arr::dot($allConfig, self::ConfigPrependKey);

            foreach ($dotAllConfig as $key => $value) {
                $container->set($key, $value);
            }
        }
    }

    /**
     * Set default timezone.
     *
     * @param ConfigRepository $config
     * @return void
     */
    private function setDefaultTimezone(ConfigRepository $config): void
    {
        /** @var string */
        $timezone = $config->get('app.timezone', 'UTC');

        date_default_timezone_set($timezone);
    }
}
