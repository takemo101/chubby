<?php

namespace Takemo101\Chubby\Support;

use ReflectionFunction;
use ReflectionNamedType;
use Closure;

class ParameterKeyTypeHintResolver
{
    /**
     * If there is a type hint in the argument of the function to be called,
     * solve the argument that matches the type hint from the parameter.
     *
     * @param callable $callable
     * @param array<string,mixed> $parameters
     * @return array<string,mixed>
     */
    public function resolve(
        callable $callable,
        array $parameters,
    ): array {
        $result = $parameters;

        $reflectionFunction = new ReflectionFunction(
            Closure::fromCallable($callable),
        );

        $reflectionParameters = $reflectionFunction->getParameters();

        foreach ($reflectionParameters as $reflection) {
            $name = $reflection->getName();
            $type = $reflection->getType();

            if ($type === null) {
                continue;
            }

            if (!($type instanceof ReflectionNamedType)) {
                continue;
            }

            if ($type->isBuiltin()) {
                continue;
            }

            $class = $type->getName();

            if ($class === 'self') {
                $class = $reflection->getDeclaringClass()->getName();
            }

            if (isset($result[$class])) {
                $result[$name] = $result[$class];

                unset($result[$class]);
            }
        }

        return $result;
    }
}
