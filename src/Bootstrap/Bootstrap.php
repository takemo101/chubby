<?php

namespace Takemo101\Chubby\Bootstrap;

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\Bootstrap\Provider\ProviderNameable;
use RuntimeException;

/**
 * Application bootstrap
 */
class Bootstrap implements Provider
{
    /**
     * @var array<string,Provider>
     */
    private array $providers = [];

    /**
     * @var array<class-string<Provider>,Provider>
     */
    private array $classes = [];

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
     * @throws RuntimeException
     */
    public function addProvider(Provider ...$providers): self
    {
        foreach ($providers as $provider) {

            $name = $provider instanceof ProviderNameable
                ? $provider->getProviderName()
                : $provider::ProviderName;

            if (isset($this->providers[$name])) {
                throw new RuntimeException(
                    sprintf(
                        'Provider name "%s" is already registered.',
                        $name,
                    ),
                );
            }

            $this->providers[$name] = $provider;
            $this->classes[get_class($provider)] = $provider;
        }

        return $this;
    }

    /**
     * Get provider by class name
     *
     * @template T of Provider
     *
     * @param class-string<T> $class
     * @return T|null
     */
    public function getProviderByClass(string $class): ?Provider
    {
        /** @var T|null */
        $result = $this->classes[$class] ?? null;

        return $result;
    }

    /**
     * Get provider by name
     *
     * @param string $name
     * @return Provider|null
     */
    public function getProviderByName(string $name): ?Provider
    {
        return $this->providers[$name] ?? null;
    }

    /**
     * Get providers
     *
     * @return array<string,Provider>
     */
    public function getProviders(): array
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
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot($container);
        }
    }
}
