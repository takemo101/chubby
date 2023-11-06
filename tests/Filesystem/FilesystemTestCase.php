<?php

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Filesystem\PathHelper;
use Takemo101\Chubby\Filesystem\SymfonyLocalFilesystem;

class FilesystemTestCase extends BaseTestCase
{
    protected LocalFilesystem $filesystem;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpLocalFilesystem();
    }

    /**
     * Create test local filesystem.
     *
     * @return void
     */
    public function setUpLocalFilesystem(): void
    {
        $this->filesystem = new SymfonyLocalFilesystem();
    }

    /**
     * Get test resource path.
     *
     * @param string $file
     * @return string
     */
    public function getTestResourcePath(string $file = ''): string
    {
        $helper = new PathHelper();

        $path = $helper->join(dirname(__DIR__, 1), 'resource/filesystem');

        return $file ? $helper->join($path, $file) : $path;
    }
}
