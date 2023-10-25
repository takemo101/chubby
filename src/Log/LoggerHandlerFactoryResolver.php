<?php

namespace Takemo101\Chubby\Log;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Log\Factory\LoggerHandlerFactory;

/**
 * LoggerHandlerFactory resolution
 */
final class LoggerHandlerFactoryResolver
{
    /**
     * constructor
     *
     * @param ApplicationContainer $container
     */
    public function __construct(
        private readonly ApplicationContainer $container,
    ) {
        //
    }

    /**
     * Resolves to factory object.
     * If unresolvable, return null.
     *
     * @param class-string<LoggerHandlerFactory>|object $factory
     * @return LoggerHandlerFactory|null
     */
    public function resolve(string|object $factory): ?LoggerHandlerFactory
    {
        return $this->getFactoryObjectOr(
            is_string($factory)
                ? $this->container->make($factory)
                : $factory
        );
    }

    /**
     * If it is a factory object, return the object as is; if it is not a factory object, return null.
     *
     * @param mixed $factory
     * @return LoggerHandlerFactory|null
     */
    private function getFactoryObjectOr(mixed $factory): ?LoggerHandlerFactory
    {
        if (is_object($factory) && $factory instanceof LoggerHandlerFactory) {
            return $factory;
        }

        return null;
    }
}
