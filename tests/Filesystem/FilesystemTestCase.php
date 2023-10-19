<?php

namespace Tests\Filesystem;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Takemo101\Chubby\Filesystem\LocalFilesystem;

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
        $this->filesystem = new LocalFilesystem();
    }

    /**
     * Get test resource path.
     *
     * @param string $file
     * @return string
     */
    public function getTestResourcePath(string $file = ''): string
    {
        $helper = $this->filesystem->helper;

        $path = $helper->join(dirname(__DIR__, 1), 'resource/filesystem');

        return $file ? $helper->join($path, $file) : $path;
    }
}
