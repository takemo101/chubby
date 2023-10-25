<?php

namespace Takemo101\Chubby\Log\Factory;

use Monolog\Handler\HandlerInterface;
use Monolog\Level;

interface LoggerHandlerFactory
{
    /**
     * Create logger handler.
     *
     * @return HandlerInterface
     */
    public function create(): HandlerInterface;
}
