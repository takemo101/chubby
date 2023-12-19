<?php

use Psr\Container\ContainerInterface;
use Takemo101\Chubby\Container\ObjectResolver;
use Takemo101\Chubby\Container\ObjectResolverException;
use Mockery as m;

describe(
    'ObjectResolver',
    function () {

        it('should resolve an object', function () {
            $container = m::mock(ContainerInterface::class);
            $container->shouldReceive('get')->with(Stringable::class)
                ->andReturn(new class implements Stringable
                {
                    public function __toString(): string
                    {
                        return 'test';
                    }
                });

            $resolver = new ObjectResolver();
            $object = $resolver->resolve(ObjectResolverTestClass::class, $container);

            expect($object)->toBeInstanceOf(ObjectResolverTestClass::class);
        });

        it('should throw an exception if the class is not instantiable', function () {
            $container = m::mock(ContainerInterface::class);

            $resolver = new ObjectResolver();

            expect(fn () => $resolver->resolve(AbstractObjectResolverTestClass::class, $container))
                ->toThrow(ObjectResolverException::class);
        });

        it('should throw an exception if the parameter type is not found', function () {
            $container = m::mock(ContainerInterface::class);

            $resolver = new ObjectResolver();

            expect(fn () => $resolver->resolve(UnionObjectResolverTestClass::class, $container))
                ->toThrow(ObjectResolverException::class);
        });

        it('should throw an exception if the parameter type is a built-in type', function () {
            $container = m::mock(ContainerInterface::class);

            $resolver = new ObjectResolver();

            expect(fn () => $resolver->resolve(BuiltInObjectResolverTestClass::class, $container))
                ->toThrow(ObjectResolverException::class);
        });

        it('should throw an exception if the container returns an invalid type', function () {
            $container = m::mock(ContainerInterface::class);
            $container->shouldReceive('get')->with(Stringable::class)->andReturn('not an object');

            $resolver = new ObjectResolver();

            expect(fn () => $resolver->resolve(ObjectResolverTestClass::class, $container))
                ->toThrow(ObjectResolverException::class);
        });

        it('should throw an exception if the parameter is variadic', function () {
            $container = m::mock(ContainerInterface::class);

            $resolver = new ObjectResolver();

            expect(fn () => $resolver->resolve(VariadicObjectResolverTestClass::class, $container))
                ->toThrow(ObjectResolverException::class);
        });
    }
)->group('ObjectResolver', 'container');

class ObjectResolverTestClass
{
    public function __construct(
        public Stringable $object,
    ) {
    }
}

abstract class AbstractObjectResolverTestClass
{
    public function __construct()
    {
    }
}

class BuiltInObjectResolverTestClass
{
    public function __construct(
        public string $string,
    ) {
        //
    }
}
class UnionObjectResolverTestClass
{
    public function __construct(
        public stdClass|Stringable $object,
    ) {
    }
}

class VariadicObjectResolverTestClass
{
    public function __construct(
        Stringable ...$objects,
    ) {
    }
}
