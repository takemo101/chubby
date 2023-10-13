<?php

namespace Takemo101\Chubby\Config;

use RuntimeException;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Filesystem\LocalSystem;
use Illuminate\Support\Arr;

/**
 * config repository
 */
class ConfigPhpRepository implements ConfigRepository
{
    /**
     * @var string
     */
    public const ConfigExtension = '.php';

    /**
     * @var LocalSystem
     */
    protected LocalSystem $filesystem;

    /**
     * @var array<string,mixed>
     */
    protected $config = [];

    /**
     * constructor
     *
     * @param LocalSystem|null $filesystem
     * @param string|null $directory
     */
    public function __construct(
        ?LocalSystem $filesystem = null,
        ?string $directory = null,
    ) {
        $this->filesystem = $filesystem ?? new LocalFilesystem();

        if ($directory) {
            $this->load($directory);
        }
    }

    /**
     * Extract the config key string from the file path.
     *
     * @param string $path
     * @return string
     */
    private function extractKeyByPath(string $path): string
    {
        return basename($path, self::ConfigExtension);
    }

    /**
     * Is there a config for the key?.
     *
     * @param string $key
     * @return boolean
     */
    public function hasKey(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    /**
     * Load configuration data from specified directory path.
     *
     * @param string $directory
     * @return void
     */
    public function load(string $directory): void
    {
        $ext = self::ConfigExtension;

        $paths = $this->filesystem->glob(
            $this->filesystem
                ->helper
                ->join($directory, "*{$ext}"),
        );

        if (empty($paths)) {
            return;
        }

        foreach ($paths as $path) {
            $key = $this->extractKeyByPath($path);

            $this->config[$key] = $path;
        }
    }

    /**
     * Get data for the specified key (specify the key using dot notation)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function loadData(string $key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            $result = $this->resolve($this->config[$key]);

            $this->config[$key] = $result;

            return $result;
        }

        return $default;
    }

    /**
     * Resolve config data
     *
     * @param string|mixed[] $config
     * @return mixed[]
     * @throws RuntimeException
     */
    private function resolve(string|array $config = []): array
    {
        if (!is_string($config)) {
            return $config;
        }

        $result = require $config;

        //　If it is not an array, it is not config data and an error occurs.
        if (!is_array($result)) {
            throw new RuntimeException("config data is not array. path: {$config}");
        }

        return $result;
    }

    /**
     * Get data for the specified key (specify the key using dot notation)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $firstKey = $this->firstDotKey($key);
        $this->loadData($firstKey);

        return Arr::get($this->config, $key, $default);
    }

    /**
     * Set data for the specified key (specify the key using dot notation)
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $firstKey = $this->firstDotKey($key);
        $this->loadData($firstKey);

        Arr::set($this->config, $key, $value);
    }

    /**
     * Does data exist for the specified key?
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        $firstKey = $this->firstDotKey($key);
        $this->loadData($firstKey);

        return Arr::has($this->config, $key);
    }

    /**
     * Extract the first dot key from the specified key.
     *
     * @param string $key
     * @return string
     */
    private function firstDotKey(string $key): string
    {
        $keys = explode('.', $key);

        return array_shift($keys);
    }

    /**
     * Implementation of ArrayAccess
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /**
     * Implementation of ArrayAccess
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Implementation of ArrayAccess
     */
    public function offsetSet($offset, $value): void
    {
        $this->set((string)$offset, $value);
    }

    /**
     * Implementation of ArrayAccess
     */
    public function offsetUnset($offset): void
    {
        // not processing
    }
}
