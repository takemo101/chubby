<?php

namespace Takemo101\Chubby\Test;

use PHPUnit\Framework\TestCase;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Application;
use LogicException;

/**
 * @mixin TestCase
 */
trait HasContainerTest
{
    /**
     * @var ApplicationContainer
     */
    private ApplicationContainer $container;

    /**
     * Set application container.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    protected function setUpContainer(Application $app): void
    {
        if (!$app->isBooted()) {
            $app->boot();
        }

        $this->container = $app;
    }

    /**
     * Get application container.
     *
     * @return ApplicationContainer
     */
    public function getContainer(): ApplicationContainer
    {
        return isset($this->container)
            ? $this->container
            : throw new LogicException('Container is not set.');
    }
}
