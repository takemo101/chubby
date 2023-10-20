<?php

namespace Takemo101\Chubby\Bootstrap;

use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Source\DefinitionSource;

/**
 * PHP-DI definitions.
 */
final class Definitions
{
    /**
     * constructor
     *
     * @param ContainerBuilder<Container> $builder
     */
    public function __construct(
        private ContainerBuilder $builder,
    ) {
        //
    }

    /**
     * Add definitions to the container.
     *
     * @param string|mixed[]|DefinitionSource ...$definitions
     * @return self
     */
    public function add(string|array|DefinitionSource ...$definitions): self
    {
        $this->builder->addDefinitions(...$definitions);

        return $this;
    }
}
