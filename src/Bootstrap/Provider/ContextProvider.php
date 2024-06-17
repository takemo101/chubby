<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use DI\Factory\RequestedEntry;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Context\ContextRepository;
use Takemo101\Chubby\Context\SingleContextRepository;
use Takemo101\Chubby\Hook\Hook;

/**
 * Context related.
 */
class ContextProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'context';

    private ?ContextRepository $repository = null;

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(
        Definitions $definitions
    ): void {
        $definitions->add(
            [
                ContextRepository::class => function (
                    Hook $hook,
                ) {
                    $repository = $this->repository ?? new SingleContextRepository();

                    $hook->do(ContextRepository::class, $repository, true);

                    return $repository;
                },
            ],
        );
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

    /**
     * Set context repository.
     *
     * @param ContextRepository $repository
     * @return self
     */
    public function setContextRepository(ContextRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }
}
