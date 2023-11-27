<?php

namespace Takemo101\Chubby\Hook;

// https://github.com/voku/php-hooks/blob/master/src/voku/helper/Hooks.php

/**
 * hook filter
 * A collection of actions.
 */
class HookFilter
{
    /**
     * @var integer
     */
    public const DefaultPriority = 50;

    /**
     * @var array<string,HookAction>
     */
    private array $actions = [];

    /**
     * constructor
     *
     * @param integer $priority Filter priority
     * @param HookAction ...$actions
     */
    public function __construct(
        private int $priority = self::DefaultPriority,
        HookAction ...$actions,
    ) {
        $this->add(...$actions);
    }

    /**
     * Adjust action priority
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
     * Add action
     *
     * @param HookAction ...$actions
     * @return self
     */
    public function add(HookAction ...$actions): self
    {
        foreach ($actions as $action) {
            $this->actions[$action->getUniqueKey()] = $action;
        }

        return $this;
    }

    /**
     * Delete target action.
     *
     * @param HookAction ...$actions
     * @return self
     */
    public function remove(HookAction ...$actions): self
    {
        foreach ($actions as $action) {
            unset($this->actions[$action->getUniqueKey()]);
        }

        return $this;
    }

    /**
     * Check if there are any actions.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return empty($this->actions);
    }

    /**
     * Clear all actions.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->actions = [];

        return $this;
    }

    /**
     * getter
     *
     * @return integer
     */
    public function priority(): int
    {
        return $this->priority;
    }

    /**
     * getter
     *
     * @return array<string,HookAction>
     */
    public function actions(): array
    {
        return $this->actions;
    }

    /**
     * Create filter from callable values.
     *
     * @param integer $priority
     * @param string|mixed[]|object $function
     * @return self
     */
    public static function fromCallable(
        int $priority,
        string|array|object $function,
    ): self {
        return new self(
            priority: $priority,
            action: new HookAction($function),
        );
    }
}
