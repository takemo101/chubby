<?php

namespace Takemo101\Chubby;

use DI\ContainerBuilder;
use Takemo101\Chubby\Bootstrap\Bootstrap;
use InvalidArgumentException;
use Takemo101\Chubby\Support\ApplicationPath;

final readonly class ApplicationOption
{
    /** @var ContainerBuilder */
    public ContainerBuilder $builder;

    /** @var Bootstrap */
    public Bootstrap $bootstrap;

    /**
     * constructor
     *
     * @param string $basePath
     * @param string $configPath
     * @param string $settingPath
     * @param string $storagePath
     * @param array $dotenvNames
     * @param ContainerBuilder|null $builder
     * @param Bootstrap|null $bootstrap
     */
    public function __construct(
        public string $basePath,
        public string $configPath = '/config',
        public string $settingPath = '/setting',
        public string $storagePath = '/storage',
        public array $dotenvNames = ['.env'],
        ?ContainerBuilder $builder = null,
        ?Bootstrap $bootstrap = null,
    ) {
        if ($configPath == $settingPath) {
            throw new InvalidArgumentException('configPath and settingPath must be different.');
        }

        $this->builder = $builder ?? (new ContainerBuilder())
            ->useAttributes(true);

        $this->bootstrap = $bootstrap ?? new Bootstrap();
    }

    /**
     * Create application path instance.
     *
     * @return ApplicationPath
     */
    public function createApplicationPath(): ApplicationPath
    {
        return new ApplicationPath(
            basePath: $this->basePath,
            configPath: $this->configPath,
            settingPath: $this->settingPath,
            storagePath: $this->storagePath,
            dotenvNames: $this->dotenvNames,
        );
    }
}
