<?php

namespace Tests\Config;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\Config\ConfigPhpRepository;
use Takemo101\Chubby\Filesystem\LocalFilesystem;

class ConfigTestCase extends BaseTestCase
{
    protected ConfigPhpRepository $repository;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setupConfigRepository();
    }

    /**
     * Create test config repository.
     *
     * @return void
     */
    public function setupConfigRepository(): void
    {
        $this->repository = new ConfigPhpRepository(
            new LocalFilesystem(),
            dirname(__DIR__, 1) . '/resource/config',
        );
    }

    /**
     * Get another directory path.
     *
     * @return string
     */
    public function getAnotherDirectoryPath(): string
    {
        return dirname(__DIR__, 1) . '/resource/another-config';
    }
}
