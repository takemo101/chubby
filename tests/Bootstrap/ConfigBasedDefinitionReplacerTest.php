<?php

use DI\Definition\FactoryDefinition;
use DI\Factory\RequestedEntry;
use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Bootstrap\Support\ConfigBasedDefinitionReplacer;
use Mockery as m;
use Takemo101\Chubby\Bootstrap\Support\DependencySupportException;

describe(
    'ConfigBasedDefinitionReplacer',
    function () {

        beforeEach(function () {
            $this->config = m::mock(ConfigRepository::class);
            $this->container = m::mock(ContainerInterface::class);
            $this->hook = m::mock(Hook::class);
            $this->entry = m::mock(RequestedEntry::class);
        });

        it('should throw an exception if entry class does not exist', function () {
            $replacer = new ConfigBasedDefinitionReplacer(stdClass::class, 'configKey');
            $this->entry->shouldReceive('getName')->andReturn('NonExistentClass');

            expect(function () use ($replacer) {
                $replacer->getDefinition('NonExistentClass');
            })->toThrow(InvalidArgumentException::class, 'Entry class not found: NonExistentClass');
        });

        it('should return a FactoryDefinition', function () {
            $replacer = new ConfigBasedDefinitionReplacer(stdClass::class, 'configKey');
            $this->entry->shouldReceive('getName')->andReturn(stdClass::class);

            $definition = $replacer->getDefinition(stdClass::class);

            expect($definition)->toBeInstanceOf(FactoryDefinition::class);
            expect($definition->getName())->toBe(stdClass::class);
        });

        it('should create an instance based on the config key', function () {

            $expected = new stdClass();

            $replacer = new ConfigBasedDefinitionReplacer(stdClass::class, 'configKey');
            $this->entry->shouldReceive('getName')->andReturn(stdClass::class);
            $this->config->shouldReceive('get')->with('configKey', stdClass::class)->andReturn(stdClass::class);
            $this->container->shouldReceive('get')->with(stdClass::class)->andReturn($expected);

            $instance = $replacer($this->config, $this->container, $this->entry);

            expect($instance)->toBe($expected);
        });

        it('should throw an exception if the instance does not implement the entry class', function () {
            $replacer = new ConfigBasedDefinitionReplacer(stdClass::class, 'configKey');
            $this->entry->shouldReceive('getName')->andReturn('ExistingClass');
            $this->config->shouldReceive('get')->with('configKey', stdClass::class)->andReturn('ConfiguredClass');
            $this->container->shouldReceive('get')->with('ConfiguredClass')->andReturn('InvalidInstance');

            expect(function () use ($replacer) {
                $replacer($this->config, $this->container, $this->entry);
            })->toThrow(DependencySupportException::class);
        });

        it('should call the hook if enabled', function () {

            $expected = new stdClass();

            $replacer = new ConfigBasedDefinitionReplacer(stdClass::class, 'configKey', true);
            $this->entry->shouldReceive('getName')->andReturn(stdClass::class);
            $this->config->shouldReceive('get')->with('configKey', stdClass::class)->andReturn('ConfiguredClass');
            $this->container->shouldReceive('get')->with('ConfiguredClass')->andReturn($expected);
            $this->container->shouldReceive('get')->with(Hook::class)->andReturn($this->hook);
            $this->hook->shouldReceive('do')->with(stdClass::class, $expected)->andReturn($expected);

            $instance = $replacer($this->config, $this->container, $this->entry);

            expect($instance)->toBe($expected);
        });

        it('should generate dependency definitions', function () {
            $dependencies = [
                'EntryClass1' => stdClass::class,
                'EntryClass2' => stdClass::class,
                'EntryClass3' => stdClass::class,
            ];
            $configKeyPrefix = 'config.prefix';
            $shouldHook = true;

            $definitions = ConfigBasedDefinitionReplacer::createDependencyDefinitions(
                $dependencies,
                $configKeyPrefix,
                $shouldHook
            );

            expect($definitions)->toBeArray();
            expect(count($definitions))->toBe(count($dependencies));

            foreach ($dependencies as $entryClass => $defaultClass) {
                $configKey = "{$configKeyPrefix}.dependencies.{$entryClass}";

                expect($definitions[$entryClass])->toBeInstanceOf(ConfigBasedDefinitionReplacer::class);
                expect($definitions[$entryClass]->defaultClass)->toBe($defaultClass);
                expect($definitions[$entryClass]->configKey)->toBe($configKey);
                expect($definitions[$entryClass]->shouldHook)->toBe($shouldHook);
            }
        });
    }
)->group('ConfigBasedDefinitionReplacer');
