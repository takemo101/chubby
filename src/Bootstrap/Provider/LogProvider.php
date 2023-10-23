<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Monolog\Level;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Log\FileLoggerFactory;
use Takemo101\Chubby\Log\LoggerFactory;
use Takemo101\Chubby\Support\ApplicationPath;

/**
 * Logger related.
 */
class LogProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'log';

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
                LoggerFactory::class => function (
                    ConfigRepository $config,
                    ApplicationPath $path,
                ): LoggerFactory {

                    /** @var string */
                    $path = $config->get('log.path', $path->getStoragePath('logs'));

                    /** @var string */
                    $filename = $config->get('log.filename', 'error.log');

                    /** @var Level */
                    $level = $config->get('log.level', Level::Debug);

                    return new FileLoggerFactory(
                        path: $path,
                        filename: $filename,
                        level: $level,
                    );
                },
                LoggerInterface::class => function (
                    ConfigRepository $config,
                    LoggerFactory $factory,
                ): LoggerInterface {

                    /** @var string|null */
                    $name = $config->get('log.name');

                    return $factory->create($name);
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
        //
    }
}
