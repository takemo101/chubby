<?php

namespace Takemo101\Chubby\Event\Attribute;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class AsEvent
{
    /**
     * constructor
     *
     * @param class-string $alias
     */
    public function __construct(
        private string $alias,
    ) {
        //
    }

    /**
     * @return class-string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }
}
