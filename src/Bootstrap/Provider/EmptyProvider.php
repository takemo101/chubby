<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Takemo101\Chubby\Bootstrap\Definitions;
use BadFunctionCallException;
use InvalidArgumentException;
use Takemo101\Chubby\ApplicationContainer;

/**
 * Empty the Provider with the intentionally set name.
 */
final readonly class EmptyProvider implements Provider, ProviderNameable
{
    /**
     * constructor
     *
     * @param string $name Provider name.
     */
    public function __construct(
        private string $name,
    ) {
        if (empty($name)) {
            throw new InvalidArgumentException('name must be set.');
        }
    }

    /**
     * Get provider name.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return $this->name;
    }

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     * @throws BadFunctionCallException
     */
    public function register(Definitions $definitions): void
    {
        //
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        //
    }
}
