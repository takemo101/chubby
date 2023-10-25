<?php

namespace Takemo101\Chubby\Log;

use Monolog\Handler\HandlerInterface;

interface LoggerHandlerFactory
{
    /**
     * Create logger handler.
     *
     * @return HandlerInterface
     */
    public function create(): HandlerInterface;
}
