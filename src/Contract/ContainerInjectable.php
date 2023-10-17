<?php

namespace Takemo101\Chubby\Contract;

use Takemo101\Chubby\ApplicationContainer;

interface ContainerInjectable
{
    /**
     * Set the application container implementation.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function setContainer(ApplicationContainer $container): void;
}
