<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Support\Environment;

/**
 * Environment variable related.
 */
class EnvironmentProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'environment';

    /**
     * constructor
     *
     * @param ApplicationPath $path
     */
    public function __construct(
        protected ApplicationPath $path,
    ) {
        //
    }

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions->add(
            [
                RepositoryInterface::class => function (): RepositoryInterface {
                    $repository = RepositoryBuilder::createWithDefaultAdapters()
                        ->addAdapter(PutenvAdapter::class)
                        ->immutable()
                        ->make();

                    Dotenv::create(
                        repository: $repository,
                        paths: $this->getDotenvPath(),
                        names: $this->path->getDotenvNames(),
                    )
                        ->load();

                    return $repository;
                },
                Environment::class => fn (RepositoryInterface $repository) => new Environment($repository),
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        //
    }

    /**
     * Get dotenv path.
     *
     * @return string
     */
    protected function getDotenvPath(): string
    {
        return $this->path->getBasePath();
    }
}
