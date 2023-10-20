<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationOption;

class ApplicationTestCase extends BaseTestCase
{
    /**
     * Get test application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        return Application::createSimple(
            ApplicationOption::from(
                basePath: __DIR__ . '/../',
            ),
        );
    }
}
