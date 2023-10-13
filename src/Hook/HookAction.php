<?php

namespace Takemo101\Chubby\Hook;

use Closure;
use InvalidArgumentException;
use RuntimeException;

/**
 * Action data executed by hook
 */
final class HookAction
{
    /**
     * @var Closure
     */
    public Closure $function;

    /**
     * @var string
     */
    public readonly string $key;

    public function __construct(
        string|array|object $function,
    ) {
        if (!is_callable($function)) {
            throw new InvalidArgumentException('The given value is not callable');
        }

        $this->key = $this->createUniqueKey($function);
        $this->function = Closure::fromCallable($function);
    }

    /**
     * Create keys from callable values
     *
     * @param string|mixed[]|object $function
     * @return string
     */
    private function createUniqueKey(string|array|object $function): string
    {
        if (is_string($function)) {
            return $function;
        }

        if (is_object($function)) {
            return spl_object_hash($function);
        }

        if (!is_array($function)) {
            throw new RuntimeException('The given value is not callable');
        }

        return (is_object($function[0])
            ? spl_object_hash($function[0])
            : $function[0]
        ) . $function[1];
    }
}
