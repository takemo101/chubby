<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationOption;
use Takemo101\Chubby\Bootstrap\Provider\ConsoleProvider;
use Takemo101\Chubby\Bootstrap\Provider\HttpProvider;
use Takemo101\Chubby\Test\HasConsoleTest;
use Takemo101\Chubby\Test\HasContainerTest;
use Takemo101\Chubby\Test\HasHttpTest;

class AppTestCase extends BaseTestCase
{
    use HasContainerTest;
    use HasHttpTest;
    use HasConsoleTest;

    /**
     * Before each test.
     */
    protected function setUp(): void
    {
        $this->setUpContainer(
            Application::createSimple(
                ApplicationOption::from(
                    basePath: __DIR__ . '/../',
                ),
            )->addProvider(
                new HttpProvider(),
                new ConsoleProvider(),
            ),
        );
        $this->setUpHttp();
        $this->setUpConsole();
    }
}
