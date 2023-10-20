<?php

namespace Takemo101\Chubby;

use Throwable;
use Exception;

class ApplicationAlreadyBootedException extends Exception
{
    /**
     * constructor.
     *
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     *
     * @return void
     */
    public function __construct(
        string $message = "Application already booted.",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
