<?php

namespace Takemo101\Chubby\Hook;

use Closure;
use InvalidArgumentException;

/**
 * Action data executed by hook
 */
final class HookAction
{
    /**
     * construct
     *
     * @param string|mixed[]|object $function
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string|array|object $function,
    ) {
        if (!is_callable($function)) {
            throw new InvalidArgumentException('The given value is not callable');
        }
    }

    /**
     * Get keys from callable values
     *
     * @param string|mixed[]|object $function
     * @return string
     */
    public function getUniqueKey(): string
    {
        $function = $this->function;

        if (is_string($function)) {
            return $function;
        }

        if (is_object($function)) {
            return spl_object_hash($function);
        }

        return (is_object($function[0])
            ? spl_object_hash($function[0])
            : $function[0]
        ) . $function[1];
    }

    /**
     * Get callable
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        return Closure::fromCallable($this->function);
    }

    /**
     * Get original value.
     *
     * @return string|mixed[]|object
     */
    public function original(): string|array|object
    {
        return $this->function;
    }
}
