<?php

use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationAlreadyBootedException;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\ApplicationOption;
use Takemo101\Chubby\Bootstrap\Bootstrap;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\ContainerInitializationException;
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Support\ApplicationSummary;
use Takemo101\Chubby\Support\Environment;
use Tests\Application\ApplicationTestCase;

describe(
    'application',
    function () {
        test(
            'Create ApplicationPath from ApplicationOption',
            function () {
                $basePath = __DIR__ . '/../../';

                $configDirectory = '/config';
                $settingDirectory = '/setting';
                $storageDirectory = '/storage';
                $dotenvNames = ['.env'];

                $option = ApplicationOption::from(
                    basePath: $basePath,
                    configPath: $configDirectory,
                    settingPath: $settingDirectory,
                    storagePath: $storageDirectory,
                    dotenvNames: $dotenvNames,
                );

                $realBasePath = realpath($basePath);

                $path = $option->createApplicationPath();

                expect($path->getBasePath())->toEqual($realBasePath);
                expect($path->getConfigPath())->toEqual($realBasePath . $configDirectory);
                expect($path->getSettingPath())->toEqual($realBasePath . $settingDirectory);
                expect($path->getStoragePath())->toEqual($realBasePath . $storageDirectory);
                expect($path->getDotenvNames())->toEqual($dotenvNames);
            },
        );

        test(
            'Provider is added to Bootstrap via Application',
            function () {
                $bootstrap = new Bootstrap();

                $app = Application::create(
                    ApplicationOption::from(
                        bootstrap: $bootstrap,
                    ),
                );

                $initialProviderCount = count($bootstrap->providers());

                expect($initialProviderCount)->toBeGreaterThan(0);

                $app->addProvider(
                    new class() implements Provider
                    {
                        public function register(Definitions $definitions): void
                        {
                            //
                        }

                        public function boot(Application $app): void
                        {
                            //
                        }
                    },
                );

                expect(count($bootstrap->providers()))->toEqual($initialProviderCount + 1);
            },
        );

        test(
            "Run Bootstrap's Provider via Application",
            function () {
                $provider = new class() implements Provider
                {
                    public const ProviderName = 'test01';

                    public bool $booted = false;

                    public bool $registered = false;

                    public function register(Definitions $definitions): void
                    {
                        $this->registered = true;
                    }

                    public function boot(Application $app): void
                    {
                        $this->booted = true;
                    }
                };

                $app = Application::create(
                    ApplicationOption::from(
                        bootstrap: new Bootstrap($provider),
                    ),
                );

                expect($provider->booted)->toBeFalse();
                expect($provider->registered)->toBeFalse();

                $app->boot();

                expect($provider->booted)->toBeTrue();
                expect($provider->registered)->toBeTrue();
            },
        );

        test(
            'Boot the application',
            function () {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                expect($app->isBooted())->toBeFalse();

                $app->boot();

                expect($app->isBooted())->toBeTrue();
            },
        );

        test(
            'If the Application is not booted, an exception will occur when using the DI container',
            function () {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                expect(fn () => $app->get(ApplicationPath::class))->toThrow(ContainerInitializationException::class);
            },
        );

        test(
            'If the Application has already been booted, an exception will occur when adding a Provider',
            function () {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                $app->boot();

                expect(fn () => $app->addProvider(
                    new class() implements Provider
                    {
                        public function register(Definitions $definitions): void
                        {
                            //
                        }

                        public function boot(Application $app): void
                        {
                            //
                        }
                    },
                ))->toThrow(ApplicationAlreadyBootedException::class);
            },
        );

        test(
            "Get the defined instance from the Application's DI container",
            function (string $class) {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                $app->boot();

                expect($app->get($class))->toBeInstanceOf($class);
            },
        )->with([
            Environment::class,
            ApplicationPath::class,
            ApplicationSummary::class,
            ApplicationContainer::class,
        ]);

        test(
            "Get ApplicationSummary with environment variables set from Application's DI container",
            function () {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                $app->boot();

                /** @var ApplicationSummary */
                $summary = $app->get(ApplicationSummary::class);

                expect($summary->env)->toEqual('testing');
                expect($summary->debug)->toBeTrue();
            },
        );

        test(
            "Obtain Environment and refer to environment variables from Application's DI container",
            function () {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                $app->boot();

                /** @var Environment */
                $environment = $app->get(Environment::class);

                expect($environment->get('APP_ENV'))->toEqual('testing');
                expect((bool) $environment->get('APP_DEBUG'))->toBeTrue();
            },
        );
    }
)->group('application');
