<?php

use DI\ContainerBuilder;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Bootstrap;
use Takemo101\Chubby\Bootstrap\BootstrapException;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Provider\ClosureProvider;
use Takemo101\Chubby\Bootstrap\Provider\Provider;
use Takemo101\Chubby\Bootstrap\Provider\ProviderNameable;

describe(
    'bootstrap',
    function () {
        test(
            'Add a Provider instance',
            function (array $providers) {
                $bootstrap = new Bootstrap();

                $bootstrap->addProvider(...$providers);

                foreach ($providers as $provider) {
                    $expected = get_class($provider);

                    $actual = $bootstrap->getProviderByName(
                        $provider instanceof ProviderNameable
                            ? $provider->getProviderName()
                            : $provider::ProviderName
                    );

                    expect($actual)->toBeInstanceOf($expected);

                    $actual = $bootstrap->getProviderByClass($expected);

                    expect($actual)->toBeInstanceOf($expected);
                }
            },
        )->with([
            fn () => [new ClosureProvider(register: fn () => [], name: 'test')],
            fn () => [new class() implements Provider
            {
                public const ProviderName = 'test';

                public function register(Definitions $definitions): void
                {
                    //
                }
                public function boot(ApplicationContainer $container): void
                {
                    //
                }
            }],
            fn () => [
                new class() implements Provider
                {
                    public const ProviderName = 'test01';

                    public function register(Definitions $definitions): void
                    {
                        //
                    }
                    public function boot(ApplicationContainer $container): void
                    {
                        //
                    }
                },
                new class() implements Provider, ProviderNameable
                {
                    public function getProviderName(): string
                    {
                        return 'test02';
                    }
                    public function register(Definitions $definitions): void
                    {
                        //
                    }
                    public function boot(ApplicationContainer $container): void
                    {
                        //
                    }
                },
            ]
        ]);

        test(
            'Exceptions occur when a provider with the same name is added',
            function () {
                $bootstrap = new Bootstrap();

                expect(
                    fn () => $bootstrap->addProvider(
                        new ClosureProvider(register: fn () => [], name: 'test'),
                        new ClosureProvider(register: fn () => [], name: 'test'),
                    ),
                )->toThrow(BootstrapException::class);
            },
        );

        test(
            'Get all the instances of the added provider',
            function () {
                $bootstrap = new Bootstrap();

                $expected = 10;

                $bootstrap->addProvider(
                    ...array_map(
                        fn (int $counter) => new ClosureProvider(register: fn () => [], name: 'test' . $counter),
                        range(0, $expected - 1)
                    ),
                );

                $actual = $bootstrap->getProviders();

                expect($actual)->toHaveLength($expected);
            },
        );

        test(
            'Execute the register method of the added provider',
            function () {
                $bootstrap = new Bootstrap();

                $provider = new class(
                    'test',
                    'test',
                ) implements Provider
                {
                    public const ProviderName = 'test';

                    public function __construct(
                        public string $definitionKey,
                        public string $definitionValue,
                    ) {
                        //
                    }
                    public function register(Definitions $definitions): void
                    {
                        $definitions->add([
                            $this->definitionKey => $this->definitionValue,
                        ]);
                    }
                    public function boot(ApplicationContainer $container): void
                    {
                        //
                    }
                };

                $bootstrap->addProvider($provider);

                $builder = new ContainerBuilder();

                $bootstrap->register(new Definitions($builder));

                $container = $builder->build();

                $actual = $container->get($provider->definitionKey);
                $expected = $provider->definitionValue;

                expect($actual)->toBe($expected);
            }
        );

        test(
            'Execute the boot method of the added provider',
            function () {
                $bootstrap = new Bootstrap();

                $expected = 'test';

                $provider = new class($expected) implements Provider
                {
                    public const ProviderName = 'test';

                    public function __construct(
                        public string $value,
                    ) {
                        //
                    }
                    public function register(Definitions $definitions): void
                    {
                        //
                    }
                    public function boot(ApplicationContainer $container): void
                    {
                        $this->value = 'boot';
                    }
                };

                $bootstrap->addProvider($provider);

                $bootstrap->boot(Mockery::mock(ApplicationContainer::class));

                $actual = $provider->value;

                expect($actual)->not->toBe($expected);
            }
        );
    }
)->group('bootstrap');
