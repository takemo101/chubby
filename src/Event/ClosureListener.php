<?php

namespace Takemo101\Chubby\Event;

use Closure;

/**
 * @implements EventListener<object>
 */
class ClosureListener implements EventListener
{
    /**
     * constructor
     *
     * @param Closure(object):void $callback
     */
    private function __construct(
        private Closure $callback,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(object $event): void
    {
        ($this->callback)($event);
    }

    /**
     * Create a new Closure listener.
     *
     * @param callable(object):void $callable
     * @return EventListener<object>
     */
    public static function from(callable $callable): EventListener
    {
        $callback = $callable instanceof Closure
            ? $callable
            : Closure::fromCallable($callable);

        return new self($callback);
    }
}
