<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\ApplicationContainer;
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
     * @var bool Should throw exception on missing dotenv.
     */
    public const ShouldThrowsExceptionOnMissingDotenv = false;

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
                RepositoryInterface::class => function (
                    LoggerInterface $logger,
                ): RepositoryInterface {
                    $repository = RepositoryBuilder::createWithDefaultAdapters()
                        ->addAdapter(PutenvAdapter::class)
                        ->immutable()
                        ->make();

                    $paths = $this->getDotenvPaths();
                    $names = $this->path->getDotenvNames();

                    try {
                        Dotenv::create(
                            repository: $repository,
                            paths: $paths,
                            names: $names,
                        )
                            ->load();
                    } catch (InvalidPathException $e) {
                        $logger->warning($e, [
                            'paths' => $paths,
                            'names' => $names,
                        ]);

                        if (static::ShouldThrowsExceptionOnMissingDotenv) {
                            throw $e;
                        }
                    }

                    return $repository;
                },
                Environment::class => fn (RepositoryInterface $repository) => new Environment($repository),
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
     * Get dotenv path.
     *
     * @return string|string[];
     */
    protected function getDotenvPaths(): string|array
    {
        return $this->path->getBasePath();
    }
}
