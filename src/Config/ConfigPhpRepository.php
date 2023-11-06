<?php

namespace Takemo101\Chubby\Config;

use RuntimeException;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Illuminate\Support\Arr;
use Takemo101\Chubby\Filesystem\PathHelper;
use Takemo101\Chubby\Filesystem\SymfonyLocalFilesystem;

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
     * @var LocalFilesystem
     */
    protected LocalFilesystem $filesystem;

    /**
     * @var array<string,mixed>
     */
    protected $config = [];

    /**
     * constructor
     *
     * @param LocalFilesystem|null $filesystem
     * @param string|null $directory
     */
    public function __construct(
        ?LocalFilesystem $filesystem = null,
        ?string $directory = null,
    ) {
        $this->filesystem = $filesystem ?? new SymfonyLocalFilesystem();

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
            (new PathHelper())->join($directory, "*{$ext}"),
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
     * @return void
     */
    private function loadData(string $key): void
    {
        if (array_key_exists($key, $this->config)) {
            /** @var string|mixed[] */
            $pathOrConfig = $this->config[$key];

            $result = $this->resolve($pathOrConfig);

            $this->config[$key] = $result;
        }
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
     * Get all data.
     *
     * @return array<string,mixed>
     */
    public function all(): array
    {
        foreach ($this->config as $key => $value) {
            /** @var string|mixed[] */
            $pathOrConfig = $value;

            if (is_string($pathOrConfig)) {
                $result = $this->resolve($pathOrConfig);

                $this->config[$key] = $result;
            }
        }

        return $this->config;
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
