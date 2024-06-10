<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use DI\Factory\RequestedEntry;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
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
     * @var string
     */
    public const EnvPrependKey = 'env';

    /**
     * @var string[] Default dotenv file names.
     */
    public const DefaultEnvNames = [
        '.env',
    ];

    /**
     * @var boolean Should throw exception on missing dotenv.
     */
    private $shouldThrowsExceptionOnMissingDotenv = false;

    /**
     * @var string[] Dotenv directory paths.
     */
    private array $paths;

    /**
     * @var string[] Dotenv file names.
     */
    private array $names;

    /**
     * constructor
     *
     * @param string[] $paths Dotenv directory paths.
     * @param string[] $names Dotenv file names.
     */
    public function __construct(
        array $paths,
        array $names = self::DefaultEnvNames,
    ) {
        $this->setDotenvPath(...$paths);
        $this->setDotenvName(...$names);
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

                    $paths = $this->paths;
                    $names = $this->names;

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

                        if ($this->shouldThrowsExceptionOnMissingDotenv) {
                            throw $e;
                        }
                    }

                    return $repository;
                },
                Environment::class => fn (RepositoryInterface $repository) => new Environment($repository),
                // Inject the value like #[Inject('env.APP_NAME')]
                self::EnvPrependKey . '.*' => function (
                    Environment $env,
                    RequestedEntry $entry,
                ) {
                    $key = (string) preg_replace(
                        '/^' . self::EnvPrependKey . '\./',
                        '',
                        $entry->getName(),
                    );

                    return $env->get($key);
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
     * Set dotenv directory path.
     *
     * @param string ...$paths
     * @return self
     */
    public function setDotenvPath(string ...$paths): self
    {
        assert(
            !empty($paths),
            'EnvironmentProvider requires at least one path.'
        );

        $this->paths = $paths;

        return $this;
    }

    /**
     * Set dotenv file name.
     *
     * @param string ...$names Dotenv file names.
     * @return self
     */
    public function setDotenvName(string ...$names): self
    {
        assert(
            !empty($names),
            'EnvironmentProvider requires at least one name.'
        );

        $this->names = $names;

        return $this;
    }

    /**
     * Set whether to throw an exception if dotenv is missing.
     *
     * @param boolean $throw
     * @return self
     */
    public function enableThrowsExceptionOnMissingDotenv(bool $throw = true): self
    {
        $this->shouldThrowsExceptionOnMissingDotenv = $throw;

        return $this;
    }
}
