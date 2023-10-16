<?php

namespace Takemo101\Chubby\Bootstrap;

use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;

/**
 * PHP-DI definitions.
 */
final class Definitions
{
    public function __construct(
        private ContainerBuilder $builder,
    ) {
        //
    }

    /**
     * Add definitions to the container.
     *
     * @param string|array|DefinitionSource ...$definitions
     * @return self
     */
    public function add(string|array|DefinitionSource ...$definitions): self
    {
        $this->builder->addDefinitions(...$definitions);

        return $this;
    }
}
