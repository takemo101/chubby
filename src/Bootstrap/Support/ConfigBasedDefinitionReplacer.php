<?php

namespace Takemo101\Chubby\Bootstrap\Support;

use DI\Definition\Definition;
use DI\Definition\FactoryDefinition;
use DI\Definition\Helper\DefinitionHelper;
use DI\Factory\RequestedEntry;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;

/**
 * Resolves dependencies by replacing DI definitions based on the configuration file.
 *
 * @template T of object
 */
class ConfigBasedDefinitionReplacer implements DefinitionHelper
{
    /**
     * Configuration key for dependencies.
     */
    public const DependencyConfigKey = 'dependencies';

    /**
     * constructor
     *
     * @param class-string $defaultClass Default class when there is no class to support entry
     * @param string $configKey Configuration key to get the class name
     * @param boolean $shouldHook Whether to hook the replacement process
     */
    public function __construct(
        private readonly string $defaultClass,
        private readonly string $configKey,
        private readonly bool $shouldHook = false,
    ) {
        assert(
            class_exists($defaultClass),
            "Default class not found: {$defaultClass}",
        );

        assert(
            empty($configKey) === false,
            'Config key is empty'
        );
    }

    /**
     * {@inheritdoc}
     *
     * The entry name for this method must be a class name or an interface name.
     *
     * @param class-string<T> $entryName Container entry class name
     * @return Definition
     * @throws InvalidArgumentException
     */
    public function getDefinition(string $entryName): Definition
    {
        // Check if the entry class exists
        if (!(
            class_exists($entryName) ||
            interface_exists($entryName)
        )) {
            throw new InvalidArgumentException("Entry class not found: {$entryName}");
        }

        return new FactoryDefinition(
            name: $entryName,
            factory: $this,
        );
    }

    /**
     * Handler to generate an instance corresponding to the config key.
     *
     * @param ConfigRepository $config
     * @param ContainerInterface $container
     * @param RequestedEntry $entry
     * @return T
     * @throws DependencySupportException
     */
    public function __invoke(
        ConfigRepository $config,
        ContainerInterface $container,
        RequestedEntry $entry,
    ): object {

        /** @var class-string<T> */
        $entryClass = $entry->getName();

        /** @var class-string<T> */
        $class = $config->get(
            $this->configKey,
            $this->defaultClass,
        );

        /** @var T */
        $instance = $container->get($class);

        if (!($instance instanceof $entryClass)) {
            throw DependencySupportException::unsupportedEntryClass(
                class: $class,
                entryClass: $entryClass,
            );
        }

        if ($this->shouldHook) {
            /** @var Hook */
            $hook = $container->get(Hook::class);

            /** @var T */
            $instance = $hook->do($entryClass, $instance);
        }

        return $instance;
    }

    /**
     * Generates an array of dependency definitions by specifying the default values for interfaces and their corresponding implementation classes.
     *
     * @param array<class-string,class-string> $defaultDependencies Default class for each entry class
     * @param string $configKeyPrefix Configuration key prefix
     * @param boolean $shouldHook Whether to hook the replacement process
     * @return array<class-string,ConfigBasedDefinitionReplacer> Dependency definitions
     */
    public static function createDependencyDefinitions(
        array $defaultDependencies,
        string $configKeyPrefix,
        bool $shouldHook = true,
    ): array {

        assert(
            empty($configKeyPrefix) === false,
            "Config key prefix is empty",
        );

        /** @var array<class-string,ConfigBasedDefinitionReplacer> */
        $definitions = [];

        $dependencyConfigKey = self::DependencyConfigKey;

        foreach ($defaultDependencies as $entryClass => $defaultClass) {
            $configKey = "{$configKeyPrefix}.{$dependencyConfigKey}.{$entryClass}";

            $definitions[$entryClass] = new self(
                defaultClass: $defaultClass,
                configKey: $configKey,
                shouldHook: $shouldHook,
            );
        }

        return $definitions;
    }
}
