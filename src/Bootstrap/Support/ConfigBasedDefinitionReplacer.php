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
 * @template TBase of object
 * @template TDef of TBase
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
     * @param class-string<TDef> $defaultClass Default class when there is no class to support entry
     * @param string $configKey Configuration key to get the class name
     * @param boolean $shouldHook Whether to hook the replacement process
     * @throws InvalidArgumentException If the default class is not found or the config key is empty
     */
    public function __construct(
        public readonly string $defaultClass,
        public readonly string $configKey,
        public readonly bool $shouldHook = false,
    ) {
        if (class_exists($defaultClass) === false) {
            throw new InvalidArgumentException("Default class not found: {$defaultClass}");
        }

        if (empty($configKey) === true) {
            throw new InvalidArgumentException("Config key is empty.");
        }
    }

    /**
     * {@inheritdoc}
     *
     * The entry name for this method must be a class name or an interface name.
     *
     * @param class-string<TBase> $entryName Container entry class name
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
     * @return TBase
     * @throws DependencySupportException
     */
    public function __invoke(
        ConfigRepository $config,
        ContainerInterface $container,
        RequestedEntry $entry,
    ): object {

        /** @var class-string<TBase> */
        $entryClass = $entry->getName();

        /** @var class-string<TBase> */
        $class = $config->get(
            $this->configKey,
            $this->defaultClass,
        );

        /** @var TBase */
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

            /** @var TBase */
            $instance = $hook->do($entryClass, $instance);
        }

        return $instance;
    }

    /**
     * Generates an array of dependency definitions by specifying the default values for interfaces and their corresponding implementation classes.
     *
     * @template B of object
     * @template D of B
     *
     * @param array<class-string<B>,class-string<D>> $dependencies Default class for each entry class
     * @param string $configKeyPrefix Configuration key prefix
     * @param boolean $shouldHook Whether to hook the replacement process
     * @return array<class-string<B>,self<B,D>> Dependency definitions
     * @throws InvalidArgumentException
     */
    public static function createDependencyDefinitions(
        array $dependencies,
        string $configKeyPrefix,
        bool $shouldHook = true,
    ): array {

        if (empty($configKeyPrefix) === true) {
            throw new InvalidArgumentException('Config key prefix is empty');
        }

        /** @var array<class-string<B>,self<B,D>> */
        $definitions = [];

        $dependencyConfigKey = self::DependencyConfigKey;

        foreach ($dependencies as $entryClass => $defaultClass) {
            $configKey = "{$configKeyPrefix}.{$dependencyConfigKey}.{$entryClass}";

            /** @var self<B,D> */
            $replacer = new self(
                defaultClass: $defaultClass,
                configKey: $configKey,
                shouldHook: $shouldHook,
            );

            $definitions[$entryClass] = $replacer;
        }

        return $definitions;
    }
}
