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
     * @param string|null $path
     * @return string
     */
    public function getBasePath(?string $path = null): string
    {
        return $path
            ? $this->helper->join($this->basePath, $path)
            : $this->basePath;
    }

    /**
     * Path to application settings
     *
     * @param string|null $path
     * @return string
     */
    public function getSettingPath(?string $path = null): string
    {
        $extendPath = $path
            ? [$this->settingPath, $path]
            : [$this->settingPath];

        return $this->getBasePath(
            $this->helper->join(...$extendPath),
        );
    }

    /**
     * Path to config directory
     *
     * @param string|null $path
     * @return string
     */
    public function getConfigPath(?string $path = null): string
    {
        $extendPath = $path
            ? [$this->configPath, $path]
            : [$this->configPath];

        return $this->getBasePath(
            $this->helper->join(...$extendPath),
        );
    }

    /**
     * Path to storage directory
     *
     * @param string|null $path
     * @return string
     */
    public function getStoragePath(?string $path = null): string
    {
        $extendPath = $path
            ? [$this->storagePath, $path]
            : [$this->storagePath];

        return $this->getBasePath(
            $this->helper->join(...$extendPath),
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
