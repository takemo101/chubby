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
use Takemo101\Chubby\Bootstrap\Bootstrap;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Provider\EnvironmentProvider;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Support\ApplicationSummary;
use RuntimeException;
use Takemo101\Chubby\Bootstrap\Provider\ConfigProvider;
use Takemo101\Chubby\Bootstrap\Provider\ConsoleProvider;
use Takemo101\Chubby\Bootstrap\Provider\DependencyProvider;
use Takemo101\Chubby\Bootstrap\Provider\ErrorProvider;
use Takemo101\Chubby\Bootstrap\Provider\FunctionProvider;
use Takemo101\Chubby\Bootstrap\Provider\HelperProvider;
use Takemo101\Chubby\Bootstrap\Provider\LogProvider;
use Takemo101\Chubby\Bootstrap\Provider\SlimProvider;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Filesystem\LocalSystem;
use Takemo101\Chubby\Support\ServiceLocator;

use function DI\get;
use function DI\create;

final class Application implements
    ContainerInterface,
    InvokerInterface,
    FactoryInterface
{
    /**
     * @var string
     */
    public const Name = 'Chubby';

    /**
     * @var string
     */
    public const Version = '0.1.0';

    /**
     * @var ApplicationPath
     */
    private ApplicationPath $path;

    /**
     * @var Bootstrap
     */
    private Bootstrap $bootstrap;

    /**
     * @var ContainerBuilder<Container>
     */
    private ContainerBuilder $builder;

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
     * @param ApplicationOption $option
     */
    public function __construct(
        ApplicationOption $option,
    ) {
        $this->path = $option->createApplicationPath();

        $this->initialize(
            $option->builder,
            $option->bootstrap,
        );

        $this->builder = $option->builder;
        $this->bootstrap = $option->bootstrap;
    }

    /**
     * Initialize the application.
     *
     * @param ContainerBuilder<Container> $builder
     * @param Bootstrap $bootstrap
     * @return void
     */
    private function initialize(
        ContainerBuilder $builder,
        Bootstrap $bootstrap,
    ): void {
        $builder->addDefinitions(
            [
                Application::class => $this,
                ApplicationPath::class => $this->path,
                ContainerInterface::class => get(Application::class),
                InvokerInterface::class => get(Application::class),
                FactoryInterface::class => get(Application::class),
                ApplicationSummary::class => function (): ApplicationSummary {
                    /** @var ConfigRepository */
                    $config = $this->make(ConfigRepository::class);

                    /** @var string */
                    $env = $config->get('app.env', 'local');

                    /** @var boolean */
                    $debug = $config->get('app.debug', true);

                    return new ApplicationSummary(
                        env: $env,
                        debug: $debug,
                    );
                },
                LocalSystem::class => create(LocalFilesystem::class),
            ],
        );

        $bootstrap->addProvider(
            new EnvironmentProvider($this->path),
            new ErrorProvider(),
            new ConfigProvider(),
            new HelperProvider(),
            new LogProvider(),
            new FunctionProvider($this->path),
            new SlimProvider(),
            new ConsoleProvider(),
            new DependencyProvider($this->path),
        );
    }

    /**
     * Add provider class instance.
     *
     * @param Provider ...$Providers
     * @return self
     */
    public function addProvider(Provider ...$Providers): self
    {
        $this->bootstrap->addProvider(...$Providers);

        return $this;
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

        ServiceLocator::initialize($this);

        $this->bootstrap->boot($this);

        $this->isBooted = true;
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
     */
    public function getContainer(): Container
    {
        return $this->container ?? throw new RuntimeException('container is not initialized.');
    }
}
