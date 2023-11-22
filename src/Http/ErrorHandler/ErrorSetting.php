<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

readonly class ErrorSetting
{
    /**
     * constructor
     *
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     */
    public function __construct(
        public bool $displayErrorDetails = false,
        public bool $logErrors = true,
        public bool $logErrorDetails = true,
    ) {
        //
    }
}
