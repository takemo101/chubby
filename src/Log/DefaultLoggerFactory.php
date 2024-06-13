<?php

namespace Takemo101\Chubby\Log;

use DI\Attribute\Inject;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Monolog\Processor\UidProcessor;
use Symfony\Component\Uid\Uuid;

/**
 * Logger factory that outputs to a file/stream.
 */
class DefaultLoggerFactory implements LoggerFactory
{
    /**
     * constructor
     *
     * @param LoggerHandlerFactoryCollection $factories
     * @param LoggerHandlerFactoryResolver $factoryResolver
     * @param LoggerProcessorCollection $processors
     * @param LoggerProcessorResolver $processorResolver
     * @param FormatterInterface $formatter
     */
    public function __construct(
        private readonly LoggerHandlerFactoryCollection $factories,
        private readonly LoggerHandlerFactoryResolver $factoryResolver,
        private readonly LoggerProcessorCollection $processors,
        private readonly LoggerProcessorResolver $processorResolver,
        private readonly FormatterInterface $formatter,
        #[Inject('config.log.name')]
        private readonly ?string $name = null,
        #[Inject('config.log.level')]
        private readonly Level $level = Level::Debug,
        #[Inject('config.log.bubble')]
        private readonly bool $bubble = true,
    ) {
        //
    }

    /**
     * Create logger.
     *
     * @return LoggerInterface
     */
    public function create(): LoggerInterface
    {
        $logger = new Logger($this->name ?? Uuid::v4()->toRfc4122());

        // Add processors.
        $processors = $this->createProcessors();

        foreach ($processors as $processor) {
            $logger->pushProcessor($processor);
        }

        // Add handlers.
        $handlers = $this->createHandlers();

        foreach ($handlers as $handler) {
            $logger->pushHandler($handler);
        }

        return $logger;
    }

    /**
     * Create default processor.
     *
     * @return ProcessorInterface
     */
    protected function createDefaultProcessor(): ProcessorInterface
    {
        return new UidProcessor();
    }

    /**
     * Create handlers.
     *
     * @return HandlerInterface[]
     */
    private function createHandlers(): array
    {
        /** @var HandlerInterface[] */
        $handlers = [];

        foreach ($this->factories->classes() as $factory) {

            $handlerFactory = $this->factoryResolver->resolve($factory);

            $handler = $handlerFactory->create();

            // Set the log level and bubble flag.
            if ($handler instanceof AbstractHandler) {
                $handler->setLevel($this->level);
                $handler->setBubble($this->bubble);
            }

            // Set the formatter.
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($this->formatter);
            }

            $handlers[] = $handler;
        }

        return $handlers;
    }

    /**
     * Create processors.
     *
     * @return ProcessorInterface[]
     */
    private function createProcessors(): array
    {
        /** @var ProcessorInterface[] */
        $processors = [];

        // Add processors.
        foreach ($this->processors->classes() as $processor) {
            $processors[] = $this->processorResolver->resolve($processor);
        }

        return $processors;
    }
}
