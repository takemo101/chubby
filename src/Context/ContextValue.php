<?php

namespace Takemo101\Chubby\Context;

/**
 * The value object that represents the value of the context.
 */
class ContextValue
{
    /**
     * constructor
     *
     * @param mixed $value
     */
    public function __construct(
        private mixed $value,
    ) {
        //
    }

    /**
     * Update the value.
     *
     * @param mixed $value
     * @return void
     */
    public function update(mixed $value): void
    {
        $this->value = $value;
    }

    /**
     * Get the value.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value;
    }
}
