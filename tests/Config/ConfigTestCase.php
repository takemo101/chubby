<?php

namespace Tests\Config;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Mime\MimeTypes;
use Takemo101\Chubby\Config\ConfigPhpRepository;
use Takemo101\Chubby\Filesystem\Mime\MockMimeTypeGuesser;
use Takemo101\Chubby\Filesystem\Mime\SymfonyMimeTypeGuesser;
use Takemo101\Chubby\Filesystem\SymfonyLocalFilesystem;

class ConfigTestCase extends BaseTestCase
{
    protected ConfigPhpRepository $repository;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpConfigRepository();
    }

    /**
     * Create test config repository.
     *
     * @return void
     */
    public function setUpConfigRepository(): void
    {
        $this->repository = new ConfigPhpRepository(
            new SymfonyLocalFilesystem(
                new SymfonyMimeTypeGuesser(),
            ),
            dirname(__DIR__, 1) . '/resource/config',
        );
    }

    /**
     * Create mock config repository.
     *
     * @param string|null $directory
     * @return ConfigPhpRepository
     */
    public function createMockConfigRepository(?string $directory = null): ConfigPhpRepository
    {
        return new ConfigPhpRepository(
            new SymfonyLocalFilesystem(
                new MockMimeTypeGuesser(),
            ),
            $directory,
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
