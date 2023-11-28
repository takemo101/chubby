<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Exception;

/**
 * Exception class for rendering by throwing the exception.
 */
class InterruptRender extends Exception
{
    /**
     * constructor
     *
     * @param mixed $renderer
     */
    public function __construct(
        private mixed $renderer,
    ) {
        parent::__construct(
            message: 'This exception is used for rendering.',
        );
    }

    /**
     * Get data to be rendering
     *
     * @return mixed
     */
    public function getRenderer(): mixed
    {
        return $this->renderer;
    }
}
