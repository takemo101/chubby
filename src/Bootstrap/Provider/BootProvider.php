<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use DI\Definition\Source\DefinitionSource;
use Psr\Container\ContainerInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Support\ServiceLocator;

/**
 * Boot related.
 */
class BootProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'boot';

    /**
     * @var array<string|mixed[]|DefinitionSource>
     */
    private array $definitions = [];

    /**
     * @var callable[]
     */
    private array $booting = [];

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions
            ->add(
                [
                    Hook::class => fn (ContainerInterface $container) => new Hook($container),
                ],
            )
            ->add(...$this->definitions);
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        ServiceLocator::initialize($container);

        foreach ($this->booting as $booting) {
            call_user_func($booting, $container);
        }
    }

    /**
     * Add definition.
     *
     * @param string|mixed[]|DefinitionSource $definition
     * @return void
     */
    public function addDefinition(string|array|DefinitionSource $definition): void
    {
        $this->definitions[] = $definition;
    }

    /**
     * Add booting.
     *
     * @param callable(ApplicationContainer):void $booting
     * @return void
     */
    public function addBoot(callable $booting): void
    {
        $this->booting[] = $booting;
    }
}
