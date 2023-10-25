<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Monolog\Level;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Log\DefaultLoggerFactory;
use Takemo101\Chubby\Log\Factory\ConsoleHandlerFactory;
use Takemo101\Chubby\Log\Factory\FileHandlerFactory;
use Takemo101\Chubby\Log\LoggerFactory;
use Takemo101\Chubby\Log\LoggerHandlerFactoryCollection;
use Takemo101\Chubby\Log\LoggerHandlerFactoryResolver;
use Takemo101\Chubby\Support\ApplicationPath;

use function DI\get;

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
                    LoggerHandlerFactoryCollection $factories,
                    LoggerHandlerFactoryResolver $resolver,
                    Hook $hook,
                ) {
                    $factory = new DefaultLoggerFactory(
                        factories: $factories,
                        resolver: $resolver,
                    );

                    /** @var LoggerFactory */
                    $factory = $hook->filter(LoggerFactory::class, $factory);

                    return $factory;
                },
                LoggerInterface::class => function (
                    ConfigRepository $config,
                    LoggerFactory $factory,
                ): LoggerInterface {

                    /** @var string|null */
                    $name = $config->get('log.name');

                    return $factory->create($name);
                },
                FileHandlerFactory::class => function (
                    ConfigRepository $config,
                    ApplicationPath $path,
                ) {
                    /** @var string */
                    $path = $config->get('log.path', $path->getStoragePath('logs'));

                    /** @var string */
                    $filename = $config->get('log.filename', 'error.log');

                    /** @var Level */
                    $level = $config->get('log.level', Level::Debug);

                    return new FileHandlerFactory(
                        path: $path,
                        filename: $filename,
                        level: $level,
                    );
                },
                ConsoleHandlerFactory::class => function (
                    ConfigRepository $config,
                ) {
                    /** @var string */
                    $stream = $config->get('log.stream', ConsoleHandlerFactory::DefaultStream);

                    /** @var Level */
                    $level = $config->get('log.level', Level::Debug);

                    return new ConsoleHandlerFactory(
                        stream: $stream,
                        level: $level,
                    );
                },
                LoggerHandlerFactoryCollection::class => function (
                    Hook $hook,
                ) {
                    $factories = new LoggerHandlerFactoryCollection(
                        FileHandlerFactory::class,
                        ConsoleHandlerFactory::class,
                    );

                    $hook->doByObject($factories);

                    return $factories;
                }
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
