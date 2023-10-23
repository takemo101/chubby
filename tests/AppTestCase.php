<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\ApplicationBuilder;
use Takemo101\Chubby\ApplicationOption;
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
            ApplicationBuilder::fromOption(
                ApplicationOption::from(
                    basePath: __DIR__ . '/../',
                ),
            )
                ->addHttp()
                ->addConsole()
                ->getApplication(),
        );
        $this->setUpHttp();
        $this->setUpConsole();
    }
}
