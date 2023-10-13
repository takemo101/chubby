<?php

namespace Takemo101\Chubby\Log;

use Psr\Log\LoggerInterface;

interface LoggerFactory
{
    /**
     * Create logger.
     *
     * @param string|null $name
     * @return LoggerInterface
     */
    public function create(?string $name = null): LoggerInterface;
}
