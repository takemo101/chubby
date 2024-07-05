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
     * constructor
     *
     * @param class-string $defaultClass Default class when there is no class to support entry
     * @param string $configKey Configuration key to get the class name
     * @param boolean $hook Whether to hook the replacement process
     */
    public function __construct(
        private readonly string $defaultClass,
        private readonly string $configKey,
        private readonly bool $hook = false,
    ) {
        //
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
     * @param Hook $hook
     * @param ContainerInterface $container
     * @param RequestedEntry $entry
     * @return T
     * @throws DependencySupportException
     */
    public function __invoke(
        ConfigRepository $config,
        Hook $hook,
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

        if ($this->hook) {
            /** @var Hook */
            $hook = $container->get(Hook::class);

            /** @var T */
            $instance = $hook->do($entryClass, $instance);
        }

        return $instance;
    }
}
