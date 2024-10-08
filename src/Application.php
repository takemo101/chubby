<?php

namespace Takemo101\Chubby;

use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Helper\DefinitionHelper;
use DI\DependencyException;
use DI\FactoryInterface;
use DI\NotFoundException;
use Invoker\Exception\InvocationException;
use Invoker\Exception\NotCallableException;
use Invoker\Exception\NotEnoughParametersException;
use Invoker\InvokerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\Mime\MimeTypes;
use Takemo101\Chubby\Bootstrap\Bootstrap;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Provider\EnvironmentProvider;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Bootstrap\Provider\BootStartProvider;
use Takemo101\Chubby\Bootstrap\Provider\ClockProvider;
use Takemo101\Chubby\Bootstrap\Provider\ConfigProvider;
use Takemo101\Chubby\Bootstrap\Provider\ContextProvider;
use Takemo101\Chubby\Bootstrap\Provider\ErrorProvider;
use Takemo101\Chubby\Bootstrap\Provider\EventProvider;
use Takemo101\Chubby\Bootstrap\Provider\HelperProvider;
use Takemo101\Chubby\Bootstrap\Provider\LogProvider;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Container\InstantContainer;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Filesystem\Mime\MimeTypeGuesser;
use Takemo101\Chubby\Filesystem\Mime\SymfonyMimeTypeGuesser;
use Takemo101\Chubby\Filesystem\PathHelper;
use Takemo101\Chubby\Filesystem\SymfonyLocalFilesystem;
use Takemo101\Chubby\Http\Uri\ApplicationUri;
use Takemo101\Chubby\Support\ApplicationSummary;
use Takemo101\Chubby\Support\ExternalEnvironmentAccessor;

use function DI\get;

class Application implements ApplicationContainer
{
    /**
     * @var string
     */
    public const Name = 'Chubby';

    /**
     * @var string
     */
    public const Version = '0.1.8';

    /**
     * @var Container|null
     */
    private ?Container $container = null;

    /**
     * @var bool
     */
    private bool $isBooted = false;

    /**
     * constructor
     *
     * @param ApplicationPath $path
     * @param Bootstrap $bootstrap
     * @param ContainerBuilder<Container> $builder
     * @param InstantContainer $instantContainer
     */
    public function __construct(
        private readonly ApplicationPath $path,
        private readonly Bootstrap $bootstrap,
        private readonly ContainerBuilder $builder,
        private readonly InstantContainer $instantContainer = new InstantContainer(),
    ) {
        $this->initialize(
            $bootstrap,
            $builder,
        );
    }

    /**
     * Initialize the application.
     *
     * @param Bootstrap $bootstrap
     * @param ContainerBuilder<Container> $builder
     * @return void
     */
    private function initialize(
        Bootstrap $bootstrap,
        ContainerBuilder $builder,
    ): void {
        $pathHelper = new PathHelper();
        $mimeTypes = MimeTypes::getDefault();
        $mimeTypeGuesser = new SymfonyMimeTypeGuesser($mimeTypes);
        $filesystem = new SymfonyLocalFilesystem($mimeTypeGuesser);
        $envAccessor = new ExternalEnvironmentAccessor();

        $this->instantContainer
            ->add($this)
            ->add(
                $this,
                ApplicationContainer::class,
            )
            ->add($this->bootstrap)
            ->add(
                $filesystem,
                LocalFilesystem::class,
            )
            ->add($this->path)
            ->add($pathHelper)
            ->add($envAccessor)
            ->add($this->builder);

        // Add a provider that satisfies the dependencies required to run the application
        $bootstrap->addProvider(
            new BootStartProvider(),
            new EnvironmentProvider(
                path: $this->path,
                envAccessor: $envAccessor,
            ),
            new ErrorProvider(),
            new ClockProvider(),
            new EventProvider(),
            new ConfigProvider(),
            new LogProvider(),
            new ContextProvider(),
            new HelperProvider(),
        );

        $builder->useAttributes(true);

        $builder->addDefinitions(
            [
                Application::class => $this,
                ApplicationPath::class => $this->path,
                ApplicationSummary::class => function (
                    ConfigRepository $config,
                ) {
                    /** @var string */
                    $uri = $config->get('app.url', 'http://localhost:8080');
                    /** @var string */
                    $name = $config->get('app.name', self::Name);
                    /** @var string */
                    $env = $config->get('app.env', 'local');
                    /** @var bool */
                    $debug = $config->get('app.debug', true);
                    /** @var bool */
                    $builtInServer = $config->get('app.built_in_server', false);

                    return new ApplicationSummary(
                        uri: ApplicationUri::fromString($uri),
                        name: $name,
                        env: $env,
                        debug: $debug,
                        builtInServer: $builtInServer,
                    );
                },
                ApplicationUri::class => fn (
                    ApplicationSummary $summary,
                ) => $summary->getUri()->copy(),
                ApplicationContainer::class => get(Application::class),
                ContainerInterface::class => get(Application::class),
                InvokerInterface::class => get(Application::class),
                FactoryInterface::class => get(Application::class),
                PathHelper::class => fn () => $pathHelper,
                LocalFilesystem::class => $filesystem,
                MimeTypeGuesser::class => $mimeTypeGuesser,
                MimeTypes::class => $mimeTypes,
                MimeTypeGuesserInterface::class => get(MimeTypes::class),
            ],
        );
    }

    /**
     * Add provider class instance.
     * Providers with the same name cannot be registered.
     * If you have been booted, throw an exception.
     *
     * @param Provider|class-string<Provider> ...$providers Provider class name or instance.
     * @return self
     * @throws ApplicationAlreadyBootedException
     */
    public function addProvider(Provider|string ...$providers): self
    {
        if ($this->isBooted()) {
            throw new ApplicationAlreadyBootedException();
        }

        $adds = [];

        foreach ($providers as $provider) {
            /** @var Provider */
            $instance = is_string($provider)
                ? $this->instantContainer->create($provider)
                : $provider;

            $adds[] = $instance;
        }

        $this->bootstrap->addProvider(...$adds);

        return $this;
    }

    /**
     * Get provider by class name.
     *
     * @template T of Provider
     *
     * @param class-string<T> $classOrName
     * @return T|null
     */
    public function getProvider(string $classOrName): ?Provider
    {
        if (class_exists($classOrName)) {
            return $this->bootstrap->getProviderByClass($classOrName);
        }

        /** @var T|null */
        $result = $this->bootstrap->getProviderByName($classOrName);

        return $result;
    }

    /**
     * Get application path.
     *
     * @return ApplicationPath
     */
    public function getPath(): ApplicationPath
    {
        return $this->path;
    }

    /**
     * Has the application been started?.
     *
     * @return boolean
     */
    public function isBooted(): bool
    {
        return $this->isBooted;
    }

    /**
     * Start the application.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->isBooted) {
            return;
        }

        $this->bootstrap->register(new Definitions($this->builder));

        $this->container = $this->builder->build();

        $this->isBooted = true;

        $this->bootstrap->boot($this);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->getContainer()->has($id);
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $name Entry name
     * @param mixed|DefinitionHelper $value Value, use definition helpers to define objects
     */
    public function set(string $name, mixed $value): void
    {
        if ($this->isBooted()) {
            $this->getContainer()->set($name, $value);
        } else {
            $this->builder->addDefinitions(
                [
                    $name => $value,
                ],
            );
        }
    }

    /**
     * Call the given function using the given parameters.
     *
     * @param callable|string[]|string $callable Function to call.
     * @param mixed[] $parameters Parameters to use.
     * @return mixed Result of the function.
     * @throws InvocationException Base exception class for all the sub-exceptions below.
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function call($callable, array $parameters = [])
    {
        return $this->getContainer()->call($callable, $parameters);
    }

    /**
     * Resolves an entry by its name. If given a class name, it will return a new instance of that class.
     *
     * @param string $name       Entry name or a class name.
     * @param mixed[]  $parameters Optional parameters to use to build the entry. Use this to force specific
     *                           parameters to specific values. Parameters not defined in this array will
     *                           be automatically resolved.
     *
     * @throws \InvalidArgumentException The name parameter must be of type string.
     * @throws DependencyException       Error while resolving the entry.
     * @throws NotFoundException         No entry or class found for the given name.
     */
    public function make(string $name, array $parameters = []): mixed
    {
        return $this->getContainer()->make($name, $parameters);
    }

    /**
     * Get the container instance.
     *
     * @return Container
     * @throws ContainerInitializationException
     */
    private function getContainer(): Container
    {
        return $this->container ?? throw new ContainerInitializationException();
    }

    /**
     * Create an instance from ApplicationOption.
     *
     * @param ApplicationOption $option
     * @return self
     */
    public static function fromOption(
        ApplicationOption $option,
    ): self {
        return new self(
            $option->createApplicationPath(),
            $option->bootstrap,
            $option->builder,
        );
    }
}
