<?php

declare(strict_types=1);

namespace Mamazu\PartialFunctions;

use ReflectionMethod;

class Functor
{
    /**
     * @param array<ReflectionParameter> $reflectionParams
     * @param array<mixed>               $values
     */
    private static function constructArguments(array $reflectionParams, array $values): array
    {
        var_dump($values);
        $resolvedArguments = [];
        foreach ($reflectionParams as $parameter) {
            /** @var ReflectionParameter $parameter */
            $resolvedArguments[] = $values[$parameter->getName()] ?? $parameter->getDefaultValue();
        }

        return $resolvedArguments;
    }

    public static function create(string $className, array $args): object
    {
        $reflection = new ReflectionMethod($className, '__construct');
        $arguments = self::constructArguments($reflection->getParameters(), $args);

        return new $className(...$arguments);
    }

    public static function call(callable $function, array $args)
    {
        $reflection = (new ReflectionFactory)->reflect($function);

        /** @var ReflectionFunction|ReflectionMethod $reflection */
        return $function(...self::constructArguments($reflection->getParameters(), $args));
    }
}
