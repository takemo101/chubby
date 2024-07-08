<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\Processor\ProcessorInterface;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Support\ConfigBasedDefinitionReplacer;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Log\DefaultLoggerFactory;
use Takemo101\Chubby\Log\Factory\FileHandlerFactory;
use Takemo101\Chubby\Log\LoggerFactory;
use Takemo101\Chubby\Log\Factory\LoggerHandlerFactory;
use Takemo101\Chubby\Log\LoggerHandlerFactoryCollection;
use Takemo101\Chubby\Log\LoggerHandlerFactoryResolver;
use Takemo101\Chubby\Log\LoggerProcessorCollection;
use Takemo101\Chubby\Log\LoggerProcessorResolver;
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
                LoggerFactory::class => get(DefaultLoggerFactory::class),
                DefaultLoggerFactory::class => function (
                    LoggerHandlerFactoryCollection $factories,
                    LoggerHandlerFactoryResolver $factoryResolver,
                    LoggerProcessorCollection $processors,
                    LoggerProcessorResolver $processorResolver,
                    FormatterInterface $formatter,
                    ConfigRepository $config,
                ) {
                    /** @var string|null */
                    $name = $config->get('log.name', null);
                    /** @var Level|integer */
                    $level = $config->get('log.level', Level::Debug);
                    /** @var bool */
                    $bubble = $config->get('log.bubble', true);

                    return new DefaultLoggerFactory(
                        factories: $factories,
                        factoryResolver: $factoryResolver,
                        processors: $processors,
                        processorResolver: $processorResolver,
                        formatter: $formatter,
                        name: $name,
                        level: is_int($level)
                            ? Level::from($level)
                            : $level,
                        bubble: $bubble,
                    );
                },
                LoggerInterface::class => function (
                    LoggerFactory $factory,
                    Hook $hook,
                ): LoggerInterface {
                    $logger = $factory->create();

                    /** @var LoggerInterface */
                    $logger = $hook->do(LoggerInterface::class, $logger);

                    return $logger;
                },
                LoggerHandlerFactoryCollection::class => function (
                    ConfigRepository $config,
                    Hook $hook,
                ) {
                    /** @var class-string<LoggerHandlerFactory>[] */
                    $handlerFactories = $config->get('log.factories', [
                        FileHandlerFactory::class,
                    ]);

                    $factories = new LoggerHandlerFactoryCollection(...$handlerFactories);

                    $hook->doTyped($factories);

                    return $factories;
                },
                LoggerProcessorCollection::class => function (
                    ConfigRepository $config,
                    Hook $hook,
                ) {
                    /** @var class-string<ProcessorInterface>[] */
                    $processors = $config->get('log.processors', [
                        UidProcessor::class,
                    ]);

                    $collection = new LoggerProcessorCollection(...$processors);

                    $hook->doTyped($collection);

                    return $collection;
                },
                FormatterInterface::class => new ConfigBasedDefinitionReplacer(
                    configKey: 'log.formatter',
                    defaultClass: LineFormatter::class,
                ),
                LineFormatter::class => fn () => new LineFormatter(
                    format: null,
                    dateFormat: null,
                    allowInlineLineBreaks: true,
                    ignoreEmptyContextAndExtra: true,
                    includeStacktraces: true,
                ),
                // This factory class ensures that it is always used as a default when the config file does not exist
                FileHandlerFactory::class => function (
                    ApplicationPath $path,
                    ConfigRepository $config,
                ) {
                    /** @var string */
                    $path = $config->get('config.log.file.path', $path->getStoragePath('logs'));
                    /** @var string */
                    $filename = $config->get('config.log.file.filename', 'error.log');
                    /** @var integer */
                    $permission = $config->get('config.log.file.permission', 0777);

                    return new FileHandlerFactory(
                        path: $path,
                        filename: $filename,
                        permission: $permission,
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
        //
    }
}
