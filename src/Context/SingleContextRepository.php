<?php

namespace Takemo101\Chubby\Context;

use LogicException;
use RuntimeException;

/**
 * Context repository for non-concurrent processing
 *
 * @todo If you need to handle Context in concurrent processing, you need to create a class that implements ContextRepository and supports coroutines.
 */
class SingleContextRepository implements ContextRepository
{
    /**
     * constructor
     *
     * @param Context $context
     */
    public function __construct(
        private ?Context $context = null,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function get(): Context
    {
        $context = $this->context;

        if (!$context) {
            throw new NotFoundContextException($this->cid());
        }

        return $context;
    }

    /**
     * {@inheritDoc}
     */
    public function has(): bool
    {
        return (bool)$this->context;
    }

    /**
     * {@inheritDoc}
     *
     * @throws LogicException If the context has already been set.
     */
    public function set(Context $context): void
    {
        if ($this->context) {
            throw new LogicException('The context has already been set.');
        }

        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     *
     * @throws RuntimeException If the process ID cannot be obtained.
     */
    public function cid(): string
    {
        return getmypid() ?: throw new RuntimeException('Failed to get the process ID.');
    }
}
