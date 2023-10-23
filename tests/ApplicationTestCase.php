<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationOption;
use Takemo101\Chubby\ApplicationBuilder;

class ApplicationTestCase extends BaseTestCase
{
    /**
     * Get test application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        return ApplicationBuilder::build(
            ApplicationOption::from(
                basePath: __DIR__ . '/../',
            ),
        );
    }
}
