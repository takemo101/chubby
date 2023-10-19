<?php

namespace Takemo101\Chubby\Bootstrap;

use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\Bootstrap\Provider\ProviderNameable;

/**
 * Application bootstrap
 */
final class Bootstrap implements Provider
{
    /**
     * @var Provider[]
     */
    private array $providers;

    /**
     * constructor
     *
     * @param Provider ...$providers
     */
    public function __construct(
        Provider ...$providers,
    ) {
        $this->addProvider(...$providers);
    }

    /**
     * Add providers
     *
     * @param Provider ...$providers
     * @return self
     */
    public function addProvider(Provider ...$providers): self
    {
        foreach ($providers as $provider) {

            $name = $provider instanceof ProviderNameable
                ? $provider->getProviderName()
                : $provider::ProviderName;

            $this->providers[$name] = $provider;
        }

        return $this;
    }

    /**
     * Get providers
     *
     * @return Provider[]
     */
    public function providers(): array
    {
        return $this->providers;
    }

    /**
     * Dependency definition registration process
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        foreach ($this->providers as $provider) {
            $provider->register($definitions);
        }
    }

    /**
     * Run all providers
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot($app);
        }
    }
}
