<?php

use Monolog\Handler\NullHandler;
use Takemo101\Chubby\Log\Factory\FileHandlerFactory;
use Takemo101\Chubby\Log\Factory\ConsoleHandlerFactory;
use Monolog\Handler\StreamHandler;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationOption;
use Takemo101\Chubby\Log\DefaultLoggerFactory;
use Takemo101\Chubby\Log\Factory\LoggerHandlerFactory;
use Takemo101\Chubby\Log\LoggerHandlerFactoryCollection;
use Takemo101\Chubby\Log\LoggerHandlerFactoryResolver;
use Psr\Log\LoggerInterface;

describe(
    'LoggerFactory',
    function () {
        test(
            'Create a Handler with LoggerHandlerFactory',
            function (LoggerHandlerFactory $factory) {
                $handler = $factory->create();

                expect($handler)->toBeInstanceOf(StreamHandler::class);
            },
        )->with([
            new FileHandlerFactory(
                path: 'test',
                filename: 'test.log',
            ),
            new ConsoleHandlerFactory(),
        ]);

        test(
            'Create Logger with DefaultLoggerFactory',
            function () {

                $mock = Mockery::mock(LoggerHandlerFactory::class);

                $mock->shouldReceive('create')
                    ->andReturn(new NullHandler());

                $factory = new DefaultLoggerFactory(
                    factories: new LoggerHandlerFactoryCollection(
                        $mock,
                    ),
                    resolver: new LoggerHandlerFactoryResolver(
                        Application::fromOption(
                            ApplicationOption::from(),
                        ),
                    ),
                );

                $logger = $factory->create();

                expect($logger)->toBeInstanceOf(LoggerInterface::class);
            },
        );
    }
)->group('LoggerFactory', 'log');
