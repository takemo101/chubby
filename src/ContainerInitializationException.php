<?php

namespace Takemo101\Chubby;

use Exception;
use Throwable;

class ContainerInitializationException extends Exception
{
    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = "Container not initialized yet",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
