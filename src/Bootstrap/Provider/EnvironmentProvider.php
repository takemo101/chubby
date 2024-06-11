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
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Support\Environment;
use Takemo101\Chubby\Support\ExternalEnvironmentAccessor;

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
     * @var string Application environment value name.
     */
    public const EnvValueName = 'APP_ENV';

    /**
     * @var boolean Should throw exception on missing dotenv.
     */
    private $shouldThrowsExceptionOnMissingDotenv = false;

    /**
     * @var string[] Dotenv directory paths.
     */
    private array $paths = [];

    /**
     * @var string[] Dotenv file names.
     */
    private array $names = [];

    /**
     * constructor
     *
     * @param ApplicationPath $path
     * @param ExternalEnvironmentAccessor $envAccessor
     */
    public function __construct(
        private readonly ApplicationPath $path,
        private readonly ExternalEnvironmentAccessor $envAccessor = new ExternalEnvironmentAccessor(),
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
                    $names = $this->getDotenvNames();

                    try {
                        $dotenv = Dotenv::create(
                            repository: $repository,
                            paths: $paths,
                            names: $names,
                        );

                        $this->shouldThrowsExceptionOnMissingDotenv
                            ? $dotenv->load()
                            : $dotenv->safeLoad();
                    } catch (InvalidPathException $e) {
                        $logger->warning($e, [
                            'paths' => $paths,
                            'names' => $names,
                        ]);

                        throw $e;
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
     * Get dotenv directory paths.
     *
     * @return string[]
     */
    private function getDotenvPaths(): array
    {
        if (!empty($this->paths)) {
            return array_unique($this->paths);
        }

        return [
            $this->path->getBasePath(),
        ];
    }

    /**
     * Set dotenv file name.
     *
     * @param string ...$names Dotenv file names.
     * @return self
     */
    public function setDotenvName(string ...$names): self
    {
        $this->names = $names;

        return $this;
    }

    /**
     * Get dotenv file names.
     * If no names are set, it will be determined from the environment.
     *
     * @return string[]
     */
    private function getDotenvNames(): array
    {
        if (!empty($this->names)) {
            return array_unique($this->names);
        }

        return $this->getDefaultDotenvNames();
    }

    /**
     * Get default dotenv file names.
     *
     * @return string[]
     */
    private function getDefaultDotenvNames(): array
    {
        /** @var string[] */
        $result = [];

        // Get APP_ENV value from external environment.
        if (
            ($env = $this->envAccessor->get(self::EnvValueName)) &&
            is_string($env)
        ) {
            // Add .env.{APP_ENV} to the list.
            $result[] = ".env.{$env}";
        }

        $result = [
            ...$result,
            ...self::DefaultEnvNames,
        ];

        return $result;
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
