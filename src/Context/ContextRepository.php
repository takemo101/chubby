<?php

namespace Takemo101\Chubby\Context;

/**
 * This is an interface for getting and setting context objects.
 */
interface ContextRepository
{
    /**
     * Gets the context.
     *
     * @return Context
     * @throws NotFoundContextException If the context is not set.
     */
    public function get(): Context;

    /**
     * Sets the context.
     *
     * @param Context $context
     * @return void
     */
    public function set(Context $context): void;

    /**
     * Determines if the context is set.
     *
     * @return bool
     */
    public function has(): bool;

    /**
     * Gets the ID of the current context.
     *
     * @return string
     */
    public function cid(): string;
}
