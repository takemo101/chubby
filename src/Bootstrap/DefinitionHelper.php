<?php

namespace Takemo101\Chubby\Bootstrap;

use Closure;
use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;

class DefinitionHelper
{
    /**
     * Create a handler to create a replacement definition by obtaining the definition of the target from a configuration
     *
     * @template T of object
     *
     * @param class-string<T> $entry Entry name to be replaced
     * @param string $configKey Configuration key to get the class name
     * @param class-string $defaultClass Default class when there is no class to support entry
     * @param boolean $hook Whether to hook the replacement process
     * @return Closure
     */
    public static function createReplaceableDefinition(
        string $entry,
        string $configKey,
        string $defaultClass,
        bool $hook = false,
    ): Closure {
        return function (
            ConfigRepository $config,
            ContainerInterface $container,
        ) use (
            $entry,
            $configKey,
            $defaultClass,
            $hook,
        ) {
            /** @var class-string<T> */
            $class = $config->get(
                $configKey,
                $defaultClass,
            );

            /** @var T */
            $instance = $container->get($class);

            if ($hook) {
                /** @var Hook */
                $hook = $container->get(Hook::class);

                /** @var T */
                $instance = $hook->do($entry, $instance);
            }

            return $instance;
        };
    }
}
