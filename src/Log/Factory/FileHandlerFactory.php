<?php

namespace Takemo101\Chubby\Log\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Takemo101\Chubby\Log\LoggerHandlerFactory;

/**
 * Create a handler that outputs logs in file format.
 */
final readonly class FileHandlerFactory implements LoggerHandlerFactory
{
    /**
     * constructor
     *
     * @param string $path
     * @param string $filename
     * @param Level $level
     */
    public function __construct(
        private string $path,
        private string $filename = 'error.log',
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
        $handler = new RotatingFileHandler(
            filename: $this->createPath(),
            maxFiles: 0,
            level: $this->level,
            bubble: true,
            filePermission: 0777,
        );

        $handler->setFormatter(
            new LineFormatter(
                format: null,
                dateFormat: null,
                allowInlineLineBreaks: true,
                ignoreEmptyContextAndExtra: true,
                includeStacktraces: true,
            ),
        );

        return $handler;
    }

    /**
     * Create log file path.
     *
     * @return string
     */
    protected function createPath(): string
    {
        return sprintf(
            '%s/%s',
            $this->path,
            $this->filename,
        );
    }
}
