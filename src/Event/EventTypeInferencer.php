<?php

namespace Takemo101\Chubby\Event;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use Takemo101\Chubby\Event\Exception\EventTypeInferenceException;

class EventTypeInferencer
{
    /**
     * @param ReflectionClass<object> $class
     * @param string $methodName Listener method name
     * @return class-string[] Event class names
     * @throws EventTypeInferenceException
     */
    public function inference(ReflectionClass $class, string $methodName): array
    {
        if (!$class->hasMethod($methodName)) {
            throw EventTypeInferenceException::notExistsMethodError($class->getName(), $methodName);
        }

        $method = $class->getMethod($methodName);

        $parameter = $method->getParameters()[0]
            ?? throw EventTypeInferenceException::notExistsParameterError($class->getName(), $methodName);

        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType) {
            return [$this->getClassFromNamedType($type)];
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->getClassFromUnionType($type);
        }

        throw EventTypeInferenceException::notExistsTypeError($class->getName(), $methodName);
    }

    /**
     * Get event class name from named type.
     *
     * @param ReflectionNamedType $type
     * @return class-string
     * @throws EventTypeInferenceException
     */
    private function getClassFromNamedType(ReflectionNamedType $type): string
    {
        if ($type->isBuiltin()) {
            throw EventTypeInferenceException::notClassTypeError($type->getName());
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
     * @throws EventTypeInferenceException
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
            EventTypeInferenceException::failedToResolveUnionTypeError();
        }

        return $classes;
    }
}
