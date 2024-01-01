<?php

namespace Takemo101\Chubby\Event\Concern;

/**
 * Stoppable event trait.
 */
trait HasStoppable
{
    /**
     * @var boolean
     */
    private bool $propagation = false;

    /**
     * {@inheritDoc}
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagation;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * @return void
     */
    public function stopPropagation(): void
    {
        $this->propagation = true;
    }
}
