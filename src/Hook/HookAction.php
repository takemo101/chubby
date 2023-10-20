<?php

namespace Takemo101\Chubby\Hook;

use Closure;
use InvalidArgumentException;

/**
 * Action data executed by hook
 */
final class HookAction
{
    /** @var string */
    public const ClassSeparator = '@';

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
        ) . self::ClassSeparator . $function[1];
    }

    /**
     * Get callable
     *
     * @return callable
     */
    public function getCallable(): callable
    {
        /** @var callable */
        $callable = $this->function;

        return Closure::fromCallable($callable);
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
