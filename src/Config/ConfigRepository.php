<?php

namespace Takemo101\Chubby\Config;

use ArrayAccess;

/**
 * コンフィグ
 *
 * @extends ArrayAccess<string,mixed>
 */
interface ConfigRepository extends ArrayAccess
{
    /**
     * Is there a config for the key?.
     *
     * @param string $key
     * @return boolean
     */
    public function hasKey(string $key): bool;

    /**
     * Load configuration data from specified directory path.
     *
     * @param string $directory
     * @param boolean $overwrite Overwrite settings with the same file name (key name)?
     * @return void
     */
    public function load(string $directory, bool $overwrite = false): void;

    /**
     * Get data for the specified key (specify the key using dot notation)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set data for the specified key (specify the key using dot notation)
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Merge data for the specified key (specify the key using dot notation)
     *
     * @param string $key
     * @param mixed[] $value
     * @param boolean $overwrite Overwrite settings with the same file name (key name)?
     * @return void
     */
    public function merge(string $key, array $value, bool $overwrite = true): void;

    /**
     * Does data exist for the specified key?
     *
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool;

    /**
     * Get all data.
     *
     * @return array<string,mixed>
     */
    public function all(): array;
}
