<?php

namespace Takemo101\Chubby\Context;

use LogicException;

/**
 * An exception that is thrown when a context is not found.
 */
class NotFoundContextException extends LogicException
{
    /**
     * constructor
     *
     * @param string $cid
     */
    public function __construct(
        private readonly string $cid,
    ) {
        parent::__construct(
            sprintf(
                'The context with the ID %s was not found.',
                $cid,
            ),
        );
    }

    /**
     * Get the ID of the context.
     *
     * @return string
     */
    public function getCid(): string
    {
        return $this->cid;
    }
}
