<?php

namespace Takemo101\Chubby\Log;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Log\Factory\LoggerHandlerFactory;

/**
 * LoggerHandlerFactory resolution
 */
class LoggerHandlerFactoryResolver
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
     * @param class-string<LoggerHandlerFactory>|LoggerHandlerFactory $factory
     * @return LoggerHandlerFactory
     */
    public function resolve(string|LoggerHandlerFactory $factory): LoggerHandlerFactory
    {
        if (!is_string($factory)) {
            return $factory;
        }

        /** @var LoggerHandlerFactory */
        $factory = $this->container->make($factory);

        return $factory;
    }
}
