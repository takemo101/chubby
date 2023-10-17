<?php

namespace Takemo101\Chubby;

use Throwable;
use Exception;

class ApplicationAlreadyBootedException extends Exception
{
    public function __construct(
        $message = "Application already booted.",
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
