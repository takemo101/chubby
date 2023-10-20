<?php

namespace Takemo101\Chubby\Console\Command;

use Takemo101\Chubby\ApplicationContainer;
use LogicException;

/**
 * Basic command.
 */
abstract class Command extends AbstractCommand
{
    /**
     * constructor
     *
     * @param ApplicationContainer $container
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        protected readonly ApplicationContainer $container,
    ) {
        parent::__construct();
    }

    /**
     * Get application container.
     *
     * @return ApplicationContainer
     */
    protected function getContainer(): ApplicationContainer
    {
        return $this->container;
    }
}
