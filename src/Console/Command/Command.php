<?php

namespace Takemo101\Chubby\Console\Command;

use Takemo101\Chubby\ApplicationContainer;

/**
 * Basic command.
 */
abstract class Command extends AbstractCommand
{
    /**
     * constructor
     *
     * @param ApplicationContainer $app
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(
        protected readonly ApplicationContainer $app,
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
        return $this->app;
    }
}
