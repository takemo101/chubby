<?php

namespace Takemo101\Chubby\Hook;

use Closure;

// https://github.com/voku/php-hooks/blob/master/src/voku/helper/Hooks.php

/**
 * hook action
 * A collection of callbacks.
 */
class HookAction
{
    /**
     * @var integer
     */
    public const DefaultPriority = 50;

    /**
     * @var Closure[]
     */
    private array $callbacks = [];

    /**
     * constructor
     *
     * @param integer $priority Action priority
     * @param HookAction ...$callbacks
     */
    public function __construct(
        private int $priority = self::DefaultPriority,
        Closure ...$callbacks,
    ) {
        $this->add(...$callbacks);
    }

    /**
     * Adjust callback priority
     * If the array given as an argument contains priorities,
     * Increment the priority to adjust to a priority that is not included.
     *
     * @param array<integer,mixed> $array Base array
     * @return integer
     */
    public function adjustPriority(array $array): int
    {
        $priority = $this->priority;

        while (true) {
            if (!isset($array[$priority])) {
                break;
            }
            $priority++;
        }

        return $this->priority = $priority;
    }

    /**
     * Add callback
     *
     * @param Closure ...$callbacks
     * @return self
     */
    public function add(Closure ...$callbacks): self
    {
        $this->callbacks = [
            ...$this->callbacks,
            ...$callbacks,
        ];

        return $this;
    }

    /**
     * Check if there are any callbacks.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->callbacks);
    }

    /**
     * Clear all callbacks.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->callbacks = [];

        return $this;
    }

    /**
     * getter
     *
     * @return integer
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * getter
     *
     * @return Closure[]
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * Create a action instance from a callable.
     *
     * @param integer $priority
     * @param callable $function
     * @return self
     */
    public static function fromCallable(
        int $priority,
        callable $function,
    ): self {
        return new self(
            priority: $priority,
            function: $function instanceof Closure
                ? $function
                : Closure::fromCallable($function),
        );
    }
}
