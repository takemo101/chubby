<?php

use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Bootstrap\DefinitionHelper;
use Mockery as m;

describe(
    'DefinitionHelper',
    function () {

        beforeEach(function () {
            $this->config = m::mock(ConfigRepository::class);
            $this->container = m::mock(ContainerInterface::class);
            $this->hook = m::mock(Hook::class);
        });

        afterEach(function () {
            m::close();
        });

        it('should return a closure', function () {
            $entry = 'MyEntry';
            $configKey = 'my_entry_config';
            $defaultClass = 'DefaultClass';
            $hook = false;

            $closure = DefinitionHelper::createReplaceableDefinition(
                $entry,
                $configKey,
                $defaultClass,
                $hook
            );

            expect($closure)->toBeInstanceOf(Closure::class);
        });

        it('should return the instance from the container without hooking', function () {
            $entry = 'MyEntry';
            $configKey = 'my_entry_config';
            $defaultClass = 'DefaultClass';
            $hook = false;

            $class = 'MyClass';
            $instance = new stdClass();

            $this->config->shouldReceive('get')
                ->once()
                ->with($configKey, $defaultClass)
                ->andReturn($class);

            $this->container->shouldReceive('get')
                ->once()
                ->with($class)
                ->andReturn($instance);

            $closure = DefinitionHelper::createReplaceableDefinition(
                $entry,
                $configKey,
                $defaultClass,
                $hook
            );

            $result = $closure($this->config, $this->container);

            expect($result)->toBe($instance);
        });

        it('should return the instance from the container with hooking', function () {
            $entry = 'MyEntry';
            $configKey = 'my_entry_config';
            $defaultClass = 'DefaultClass';
            $hook = true;

            $class = 'MyClass';
            $instance = new stdClass();
            $hookedInstance = new stdClass();

            $this->config->shouldReceive('get')
                ->once()
                ->with($configKey, $defaultClass)
                ->andReturn($class);

            $this->container->shouldReceive('get')
                ->once()
                ->with($class)
                ->andReturn($instance);

            $this->container->shouldReceive('get')
                ->once()
                ->with(Hook::class)
                ->andReturn($this->hook);

            $this->hook->shouldReceive('do')
                ->once()
                ->with($entry, $instance)
                ->andReturn($hookedInstance);

            $closure = DefinitionHelper::createReplaceableDefinition(
                $entry,
                $configKey,
                $defaultClass,
                $hook
            );

            $result = $closure($this->config, $this->container);

            expect($result)->toBe($hookedInstance);
        });
    }
)->group('DefinitionHelper', 'bootstrap');
