<?php

namespace Takemo101\Chubby\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class ObjectResolver
{
    /**
     *
     * @template T of object
     *
     * @param class-string<T> $id
     * @param ContainerInterface $container
     * @return T
     * @throws ObjectResolverException|NotFoundDependencyException
     */
    public function resolve(
        string $id,
        ContainerInterface $container,
    ): object {
        $reflectionClass = new ReflectionClass($id);

        if (!$reflectionClass->isInstantiable()) {
            throw ObjectResolverException::notInstantiable($id);
        }

        $constructor = $reflectionClass->getConstructor();

        if (is_null($constructor)) {
            return new ($id);
        }

        $parameters = $constructor->getParameters();

        $arguments = $this->createConstructorArguments(
            $parameters,
            $container,
        );

        /** @var T */
        $instance = $reflectionClass->newInstanceArgs($arguments);

        return $instance;
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @param ContainerInterface $container
     * @return array<string,object>
     * @throws ObjectResolverException|NotFoundDependencyException
     */
    private function createConstructorArguments(
        array $parameters,
        ContainerInterface $container,
    ): array {
        $arguments = [];

        foreach ($parameters as $parameter) {
            if ($parameter->isVariadic()) {
                throw ObjectResolverException::notSupportedVariadicParameter(
                    $parameter->getName(),
                );
            }

            $type = $parameter->getType();

            if (!($type instanceof ReflectionNamedType)) {
                throw ObjectResolverException::notFoundParameterType(
                    $parameter->getName(),
                );
            }

            if ($type->isBuiltin()) {
                throw ObjectResolverException::notSupportedBuiltInType(
                    $parameter->getName(),
                );
            }

            /** @var class-string */
            $id = $type->getName();

            $instance = $container->get($id);

            if (!is_object($instance)) {
                throw ObjectResolverException::invalidContainerReturnType(
                    $id,
                );
            }

            $arguments[$parameter->getName()] = $instance;
        }

        return $arguments;
    }
}
