<?php

namespace Takemo101\Chubby;

use Exception;
use Throwable;

class ContainerInitializationException extends Exception
{
    public function __construct(
        $message = "Container not initialized yet",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
