<?php

namespace Takemo101\Chubby\Log;

use Psr\Log\LoggerInterface;

interface LoggerFactory
{
    /**
     * Create logger.
     *
     * @return LoggerInterface
     */
    public function create(): LoggerInterface;
}
