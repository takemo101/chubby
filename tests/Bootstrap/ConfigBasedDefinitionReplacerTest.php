<?php

use DI\Definition\FactoryDefinition;
use DI\Factory\RequestedEntry;
use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Bootstrap\Support\ConfigBasedDefinitionReplacer;
use Mockery as m;
use Takemo101\Chubby\Bootstrap\Support\DependencySupportException;

beforeEach(function () {
    $this->config = m::mock(ConfigRepository::class);
    $this->hook = m::mock(Hook::class);
    $this->container = m::mock(ContainerInterface::class);
    $this->entry = m::mock(RequestedEntry::class);
});

describe(
    'ConfigBasedDefinitionReplacer',
    function () {

        it('should throw an exception if entry class does not exist', function () {
            $replacer = new ConfigBasedDefinitionReplacer('DefaultClass', 'configKey');
            $this->entry->shouldReceive('getName')->andReturn('NonExistentClass');

            expect(function () use ($replacer) {
                $replacer->getDefinition('NonExistentClass');
            })->toThrow(InvalidArgumentException::class, 'Entry class not found: NonExistentClass');
        });

        it('should return a FactoryDefinition', function () {
            $replacer = new ConfigBasedDefinitionReplacer('DefaultClass', 'configKey');
            $this->entry->shouldReceive('getName')->andReturn(stdClass::class);

            $definition = $replacer->getDefinition(stdClass::class);

            expect($definition)->toBeInstanceOf(FactoryDefinition::class);
            expect($definition->getName())->toBe(stdClass::class);
        });

        it('should create an instance based on the config key', function () {

            $expected = new stdClass();

            $replacer = new ConfigBasedDefinitionReplacer('DefaultClass', 'configKey');
            $this->entry->shouldReceive('getName')->andReturn(stdClass::class);
            $this->config->shouldReceive('get')->with('configKey', 'DefaultClass')->andReturn(stdClass::class);
            $this->container->shouldReceive('get')->with(stdClass::class)->andReturn($expected);

            $instance = $replacer($this->config, $this->hook, $this->container, $this->entry);

            expect($instance)->toBe($expected);
        });

        it('should throw an exception if the instance does not implement the entry class', function () {
            $replacer = new ConfigBasedDefinitionReplacer('DefaultClass', 'configKey');
            $this->entry->shouldReceive('getName')->andReturn('ExistingClass');
            $this->config->shouldReceive('get')->with('configKey', 'DefaultClass')->andReturn('ConfiguredClass');
            $this->container->shouldReceive('get')->with('ConfiguredClass')->andReturn('InvalidInstance');

            expect(function () use ($replacer) {
                $replacer($this->config, $this->hook, $this->container, $this->entry);
            })->toThrow(DependencySupportException::class);
        });

        it('should call the hook if enabled', function () {

            $expected = new stdClass();

            $replacer = new ConfigBasedDefinitionReplacer('DefaultClass', 'configKey', true);
            $this->entry->shouldReceive('getName')->andReturn(stdClass::class);
            $this->config->shouldReceive('get')->with('configKey', 'DefaultClass')->andReturn('ConfiguredClass');
            $this->container->shouldReceive('get')->with('ConfiguredClass')->andReturn($expected);
            $this->container->shouldReceive('get')->with(Hook::class)->andReturn($this->hook);
            $this->hook->shouldReceive('do')->with(stdClass::class, $expected)->andReturn($expected);

            $instance = $replacer($this->config, $this->hook, $this->container, $this->entry);

            expect($instance)->toBe($expected);
        });
    }
)->group('ConfigBasedDefinitionReplacer');
