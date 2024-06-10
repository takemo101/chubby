<?php

namespace Takemo101\Chubby\Log\Factory;

use DI\Attribute\Inject;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\HandlerInterface;

/**
 * Create a handler that outputs logs in file format.
 * Create a RotatingFileHandler.
 */
class FileHandlerFactory implements LoggerHandlerFactory
{
    /**
     * constructor
     *
     * @param string $path
     * @param string $filename
     */
    public function __construct(
        #[Inject('config.log.file.path')]
        private readonly string $path,
        #[Inject('config.log.file.filename')]
        private readonly string $filename = 'error.log',
        #[Inject('config.log.file.permission')]
        private readonly int $permission = 0777,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function create(): HandlerInterface
    {
        $handler = new RotatingFileHandler(
            filename: $this->createPath(),
            maxFiles: 0,
            filePermission: $this->permission,
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
