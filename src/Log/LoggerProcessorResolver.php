<?php

namespace Takemo101\Chubby\Log;

use Monolog\Processor\ProcessorInterface;
use Takemo101\Chubby\ApplicationContainer;

/**
 * ProcessorInterface resolution
 */
class LoggerProcessorResolver
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
     * Resolves to processor object.
     * If unresolvable, return null.
     *
     * @param class-string<ProcessorInterface>|ProcessorInterface $processor
     * @return ProcessorInterface
     */
    public function resolve(string|ProcessorInterface $processor): ProcessorInterface
    {
        if (!is_string($processor)) {
            return $processor;
        }

        /** @var ProcessorInterface */
        $processor = $this->container->make($processor);

        return $processor;
    }
}
