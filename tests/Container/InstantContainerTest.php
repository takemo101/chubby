<?php

use Takemo101\Chubby\Container\InstantContainer;
use Takemo101\Chubby\Container\ObjectResolver;
use Takemo101\Chubby\Container\NotFoundDependencyException;
use Mockery as m;

describe(
    'InstantContainer',
    function () {

        it('should set and get an instance', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);
            $instance = new stdClass();

            $container->add($instance);

            expect($container->get(stdClass::class))->toBe($instance);
        });

        it('should throw an exception when setting an instance that is not an instance of the id', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);
            $instance = new stdClass();

            expect(fn () => $container->add($instance, Stringable::class))
                ->toThrow(LogicException::class, '[Stringable] is not instance of [stdClass]');
        });

        it('should create an instance', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);
            $instance = new stdClass();

            $container->add($instance);

            $createdInstance = $container->create(stdClass::class);

            expect($createdInstance)->toBeInstanceOf(stdClass::class);
        });

        it('should throw an exception when creating an instance of a non-existent class', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);

            expect(fn () => $container->create('NonExistentClass'))
                ->toThrow(InvalidArgumentException::class, '[NonExistentClass] is not found');
        });

        it('should get an instance', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);
            $instance = new stdClass();

            $container->add($instance);

            $retrievedInstance = $container->get(stdClass::class);

            expect($retrievedInstance)->toBe($instance);
        });

        it('should throw an exception when getting a non-existent instance', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);

            expect(fn () => $container->get(stdClass::class))
                ->toThrow(NotFoundDependencyException::class, '[stdClass] is not found');
        });

        it('should check if an instance exists', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);
            $instance = new stdClass();

            $container->add($instance);

            expect($container->has(stdClass::class))->toBeTrue();
            expect($container->has('NonExistentClass'))->toBeFalse();
        });

        it('should get all dependencies', function () {
            $resolver = m::mock(ObjectResolver::class);
            $resolver->shouldReceive('resolve')->andReturn(new stdClass());

            $container = new InstantContainer($resolver);
            $instance1 = new stdClass();
            $instance2 = new class implements Stringable
            {
                public function __toString(): string
                {
                    return 'test';
                }
            };

            $container->add($instance1);
            $container->add($instance2, Stringable::class);

            $dependencies = $container->dependencies();

            expect($dependencies)->toBe([
                stdClass::class => $instance1,
                Stringable::class => $instance2,
            ]);
        });
    }
)->group('InstantContainer', 'container');
