<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

final readonly class ErrorSetting
{
    /**
     * constructor
     *
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     */
    public function __construct(
        public bool $displayErrorDetails,
        public bool $logErrors,
        public bool $logErrorDetails
    ) {
        //
    }
}
