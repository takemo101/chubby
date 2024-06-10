<?php

namespace Takemo101\Chubby\Log\Factory;

use DI\Attribute\Inject;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;

/**
 * Create a handler to output logs in the stream.
 * Create a StreamHandler.
 */
class ConsoleHandlerFactory implements LoggerHandlerFactory
{
    public const DefaultStream = 'php://stdout';

    /**
     * constructor
     *
     * @param string $stream
     * @param int $permission
     */
    public function __construct(
        #[Inject('config.log.console.stream')]
        private readonly string $stream = self::DefaultStream,
        #[Inject('config.log.console.permission')]
        private readonly int $permission = 0777,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function create(): HandlerInterface
    {
        $handler = new StreamHandler(
            stream: $this->stream,
            filePermission: $this->permission,
        );

        return $handler;
    }
}
