<?php

namespace Takemo101\Chubby\Support;

use Takemo101\Chubby\Filesystem\PathHelper;

/**
 * Application path setting
 */
final readonly class ApplicationPath
{
    private PathHelper $helper;

    /**
     * constructor
     *
     * @param string $basePath application basepath
     * @param string $settingPath setting directory path
     * @param string $configPath config directory path
     * @param string $storagePath storage directory path
     * @param string[] $dotenvNames dotenv file names
     */
    public function __construct(
        public string $basePath,
        public string $settingPath = '/setting',
        public string $configPath = '/config',
        public string $storagePath = '/storage',
        public array $dotenvNames = ['.env'],
    ) {
        $this->helper = new PathHelper();
    }

    /**
     * ベースパス取得
     *
     * @param string ...$paths
     * @return string
     */
    public function getBasePath(string ...$paths): string
    {
        return $this->helper->join($this->basePath, ...$paths);
    }

    /**
     * Path to application settings
     *
     * @param string ...$paths
     * @return string
     */
    public function getSettingPath(string ...$paths): string
    {
        return $this->getBasePath(
            $this->settingPath,
            ...$paths
        );
    }

    /**
     * Path to config directory
     *
     * @param string ...$paths
     * @return string
     */
    public function getConfigPath(string ...$paths): string
    {
        return $this->getBasePath(
            $this->configPath,
            ...$paths
        );
    }

    /**
     * Path to storage directory
     *
     * @param string ...$paths
     * @return string
     */
    public function getStoragePath(string ...$paths): string
    {
        return $this->getBasePath(
            $this->storagePath,
            ...$paths
        );
    }

    /**
     * Get dotenv file names
     *
     * @return string[]
     */
    public function getDotenvNames(): array
    {
        return $this->dotenvNames;
    }
}
