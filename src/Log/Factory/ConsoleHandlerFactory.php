<?php

namespace Takemo101\Chubby\Log\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Takemo101\Chubby\Log\LoggerHandlerFactory;

/**
 * Create a handler to output logs in the stream.
 */
final readonly class ConsoleHandlerFactory implements LoggerHandlerFactory
{
    public const DefaultStream = 'php://stdout';

    /**
     * constructor
     *
     * @param string $stream
     * @param string $filename
     * @param Level $level
     */
    public function __construct(
        private mixed $stream = self::DefaultStream,
        private Level $level = Level::Debug,
    ) {
        //
    }

    /**
     * Create logger handler.
     *
     * @return HandlerInterface
     */
    public function create(): HandlerInterface
    {
        $handler = new StreamHandler($this->stream, $this->level);

        $handler->setFormatter(
            new LineFormatter(
                format: null,
                dateFormat: null,
                allowInlineLineBreaks: false,
                ignoreEmptyContextAndExtra: true,
                includeStacktraces: false,
            ),
        );

        return $handler;
    }
}
