<?php

namespace Takemo101\Chubby;

use DI\Definition\Helper\DefinitionHelper;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;

interface ApplicationContainer extends
    ContainerInterface,
    InvokerInterface,
    FactoryInterface
{
    /**
     * Define an object or a value in the container.
     *
     * @param string $name Entry name
     * @param mixed|DefinitionHelper $value Value, use definition helpers to define objects
     */
    public function set(string $name, mixed $value): void;
}
