<?php

declare(strict_types=1);

namespace Mamazu\PartialFunctions;

use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;

class PartialFunction
{
    private static $internalDefaultValues = [
        'strpos' => ['offset' => 0],
    ];

    /** @var ReflectionMethod|ReflectionFunction */
    private $function;

    /** @var array<string> */
    private $missingArguments;

    /** @var array<mixed> */
    private $arguments = [];

    /**
     * @param ReflectionMethod|ReflectionFunction $reflection 
     * @param array<mixed> $missingArguments
     */
    public function __construct($reflection, array $missingArguments)
    {
        $this->function = $reflection;
        $this->missingArguments = $missingArguments;
    }

    public function isPartial(array $additionalArguments = []): bool
    {
        $missingArguments = array_diff($this->missingArguments, array_keys($additionalArguments));

        return count($missingArguments) > 0;
    }

    public function getMissingArguments(): array
    {
        return $this->missingArguments;
    }

    public function apply(array $arguments): self
    {
        $this->arguments = array_merge($this->arguments, $arguments);
        $this->missingArguments = array_diff($this->missingArguments, array_keys($arguments));

        return $this;
    }

    /**
     * @param array<ReflectionParameter> $reflectionParams
     * @param array<mixed> $values
     */
    public function constructArguments(array $values): array
    {
        $resolvedArguments = [];
        foreach ($this->function->getParameters() as $parameter) {
            if (array_key_exists($parameter->getName(), $values)) {
                $argument = $values[$parameter->getName()];
            } elseif (PHP_VERSION_ID < 80000 && $this->function->isInternal()) {
                // From php 8 on we can also get the default value for functions. For older versions we need a look up table
                $argument = self::$internalDefaultValues[$this->function->getName()][$parameter->getName()];
            } else {
                $argument = $parameter->getDefaultValue();
            }

            $resolvedArguments[] = $argument;
        }

        return $resolvedArguments;
    }

    public function call(array $arguments = [])
    {
        if ($this->isPartial($arguments)) {
            throw new InvalidArgumentException(
                'You can not call a partial function please provide values for the following parameters: '.implode(
                    ', ',
                    $this->missingArguments
                )
            );
        }

        $arguments = $this->constructArguments(array_merge($arguments, $this->arguments));

        return $this->function->invokeArgs($arguments);
    }

    public function __invoke(...$arguments)
    {
        $preparedArguments = [];

        // Reindex the missing arguments to ignore the already filled ones.
        $missingArguments = array_values($this->missingArguments);
        foreach ($arguments as $index => $argumentValue) {
            $preparedArguments[$missingArguments[$index]] = $argumentValue;
        }

        return $this->call($preparedArguments);
    }
}
