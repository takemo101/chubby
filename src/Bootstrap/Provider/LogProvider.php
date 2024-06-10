<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\ProcessorInterface;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\DefinitionHelper;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Log\DefaultLoggerFactory;
use Takemo101\Chubby\Log\Factory\FileHandlerFactory;
use Takemo101\Chubby\Log\LoggerFactory;
use Takemo101\Chubby\Log\Factory\LoggerHandlerFactory;
use Takemo101\Chubby\Log\LoggerHandlerFactoryCollection;
use Takemo101\Chubby\Log\LoggerProcessorCollection;

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
                FormatterInterface::class => DefinitionHelper::createReplaceable(
                    entry: FormatterInterface::class,
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
