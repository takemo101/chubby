<?php

namespace Takemo101\Chubby\Event;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;

class EventTypeInferencer
{
    /**
     * @param ReflectionClass<object> $class
     * @param string $methodName Listener method name
     * @return class-string[] Event class names
     * @throws RuntimeException
     */
    public function inference(ReflectionClass $class, string $methodName): array
    {
        if (!$class->hasMethod($methodName)) {
            throw new RuntimeException(
                sprintf(
                    'The method %s::%s() does not exist.',
                    $class->getName(),
                    $methodName,
                ),
            );
        }

        $method = $class->getMethod($methodName);

        $parameter = $method->getParameters()[0]
            ?? throw new RuntimeException(
                sprintf(
                    'The method %s::%s() must have at least one parameter.',
                    $class->getName(),
                    $methodName,
                ),
            );

        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType) {
            return [$this->getClassFromNamedType($type)];
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->getClassFromUnionType($type);
        }

        throw new RuntimeException(
            sprintf(
                'The method %s::%s() must have a type.',
                $class->getName(),
                $methodName,
            ),
        );
    }

    /**
     * Get event class name from named type.
     *
     * @param ReflectionNamedType $type
     * @return class-string
     */
    private function getClassFromNamedType(ReflectionNamedType $type): string
    {
        if ($type->isBuiltin()) {
            throw new RuntimeException(
                sprintf(
                    'The type %s is not a class.',
                    $type->getName(),
                ),
            );
        }

        /** @var class-string */
        $class = $type->getName();

        return $class;
    }

    /**
     * Get event class names from union type.
     *
     * @param ReflectionUnionType $type
     * @return class-string[]
     */
    private function getClassFromUnionType(ReflectionUnionType $type): array
    {
        /** @var class-string[] */
        $classes = [];

        foreach ($type->getTypes() as $t) {
            if ($t instanceof ReflectionNamedType) {
                $classes[] = $this->getClassFromNamedType($t);
            }
        }

        if (empty($classes)) {
            throw new RuntimeException(
                'Failed to resolve union type.',
            );
        }

        return $classes;
    }
}
