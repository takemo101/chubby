<?php

use Takemo101\Chubby\ApplicationBuilder;
use Takemo101\Chubby\ApplicationOption;
use Takemo101\Chubby\Bootstrap\Bootstrap;
use Takemo101\Chubby\Bootstrap\Provider\ConsoleProvider;
use Takemo101\Chubby\Bootstrap\Provider\DependencyProvider;
use Takemo101\Chubby\Bootstrap\Provider\FunctionProvider;
use Takemo101\Chubby\Bootstrap\Provider\HttpProvider;

describe(
    'application builder',
    function () {
        test(
            'Add Provider to Application by ApplicationBuilder',
            function (string $builderMethod, string $providerClass) {
                $bootstrap = new Bootstrap();

                $builder = ApplicationBuilder::fromOption(
                    ApplicationOption::from(
                        bootstrap: $bootstrap,
                    ),
                );

                $builder->{$builderMethod}();

                $actualProviderClassNames = array_map(
                    fn ($provider) => get_class($provider),
                    $bootstrap->getProviders(),
                );

                expect($actualProviderClassNames)->toContain($providerClass);
            },
        )->with([
            [
                'addHttp', HttpProvider::class,
            ],
            [
                'addConsole', ConsoleProvider::class,
            ],
            [
                'addFunctionSetting', FunctionProvider::class,
            ],
            [
                'addDependencySetting', DependencyProvider::class,
            ]
        ]);

        test(
            'Add the required providers using the buildStandard method',
            function () {
                $bootstrap = new Bootstrap();

                ApplicationBuilder::buildStandard(
                    ApplicationOption::from(
                        bootstrap: $bootstrap,
                    ),
                );

                $actualProviderClassNames = array_map(
                    fn ($provider) => get_class($provider),
                    $bootstrap->getProviders(),
                );

                foreach ([
                    HttpProvider::class,
                    ConsoleProvider::class,
                    FunctionProvider::class,
                    DependencyProvider::class,
                ] as $excepted) {
                    expect($actualProviderClassNames)->toContain($excepted);
                }
            }
        );
    }
)->group('application-builder');
