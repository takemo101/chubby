<?php

namespace Takemo101\Chubby\Log;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Processor\UidProcessor;
use Symfony\Component\Uid\Uuid;

/**
 * Logger factory that outputs to a file/stream.
 */
final readonly class DefaultLoggerFactory implements LoggerFactory
{
    /**
     * constructor
     *
     * @param LoggerHandlerFactoryCollection $factories
     * @param LoggerHandlerFactoryResolver $resolver
     */
    public function __construct(
        private LoggerHandlerFactoryCollection $factories,
        private LoggerHandlerFactoryResolver $resolver,
    ) {
        //
    }

    /**
     * Create logger.
     *
     * @param string|null $name
     * @return LoggerInterface
     */
    public function create(?string $name = null): LoggerInterface
    {
        $logger = new Logger($name ?? Uuid::v4()->toRfc4122());

        $logger->pushProcessor(new UidProcessor());

        $handlers = $this->createHandlers();

        foreach ($handlers as $handler) {
            $logger->pushHandler($handler);
        }

        return $logger;
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
            if ($handler = $this->resolver->resolve($factory)) {
                $handlers[] = $handler->create();
            }
        }

        return $handlers;
    }
}
