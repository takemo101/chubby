<?php

namespace Takemo101\Chubby;

use DI\Container;
use DI\ContainerBuilder;
use Takemo101\Chubby\Bootstrap\Bootstrap;
use Takemo101\Chubby\Support\ApplicationPath;
use InvalidArgumentException;

readonly class ApplicationOption
{
    /** @var string */
    public const DefaultBasePath = '/';

    /** @var string */
    public const DefaultConfigPath = '/config';

    /** @var string */
    public const DefaultSettingPath = '/setting';

    /** @var string */
    public const DefaultStoragePath = '/storage';

    /**
     * constructor
     *
     * @param string $basePath
     * @param string $configPath
     * @param string $settingPath
     * @param string $storagePath
     * @param ContainerBuilder<Container> $builder
     * @param Bootstrap $bootstrap
     */
    public function __construct(
        public string $basePath,
        public string $configPath,
        public string $settingPath,
        public string $storagePath,
        public ContainerBuilder $builder,
        public Bootstrap $bootstrap,
    ) {
        if (empty($basePath)) {
            throw new InvalidArgumentException('basePath is required.');
        }

        if ($configPath == $settingPath) {
            throw new InvalidArgumentException('configPath and settingPath must be different.');
        }
    }

    /**
     * Create application path instance.
     *
     * @return ApplicationPath
     */
    public function createApplicationPath(): ApplicationPath
    {
        return new ApplicationPath(
            basePath: realpath($this->basePath) ?: $this->basePath,
            configPath: $this->configPath,
            settingPath: $this->settingPath,
            storagePath: $this->storagePath,
        );
    }

    /**
     * Create an instance from any value.
     *
     * @param string|null $basePath
     * @param string|null $configPath
     * @param string|null $settingPath
     * @param string|null $storagePath
     * @param ContainerBuilder<Container>|null $builder
     * @param Bootstrap|null $bootstrap
     * @return self
     */
    public static function from(
        ?string $basePath = null,
        ?string $configPath = null,
        ?string $settingPath = null,
        ?string $storagePath = null,
        ?ContainerBuilder $builder = null,
        ?Bootstrap $bootstrap = null,
    ): self {
        return new self(
            basePath: empty($basePath)
                ? (getcwd() ?: self::DefaultBasePath)
                : $basePath,
            configPath: empty($configPath) ? self::DefaultConfigPath : $configPath,
            settingPath: empty($settingPath) ? self::DefaultSettingPath : $settingPath,
            storagePath: empty($storagePath) ? self::DefaultStoragePath : $storagePath,
            builder: $builder ?? new ContainerBuilder(),
            bootstrap: $bootstrap ?? new Bootstrap(),
        );
    }
}
