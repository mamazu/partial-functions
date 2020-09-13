<?php

declare(strict_types=1);

namespace Mamazu\PartialFunctions;

use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;

class PartialFunction {
    /** @var ReflectionMethod|ReflectionFunction */
    private $function;

    /** @var array<string> */
    private $missingArguments;
    
    /** @var ReflectionFactory */
    private $reflectionFactory;

    /** @var array<mixed> */
    private $arguments = [];

    /** @param ReflectionMethod|ReflectionFunction $reflection */
    public function __construct($reflection, array $missingArguments) {
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

    public function apply(array $arguments): self {
        $this->arguments = array_merge($this->arguments, $arguments);
        $this->missingArguments = array_diff($this->missingArguments, array_keys($arguments));

        return $this;
    }

    /**
     * @param array<ReflectionParameter> $reflectionParams
     * @param array<mixed>               $values
     */
    public function constructArguments(array $values): array
    {
        $resolvedArguments = [];
        var_dump($values);
        foreach ($this->function->getParameters() as $parameter) {
            /** @var ReflectionParameter $parameter */
            $resolvedArguments[] = $values[$parameter->getName()] ?? $parameter->getDefaultValue();
        }

        return $resolvedArguments;
    }

    public function call(array $arguments = []) {
        if ($this->isPartial($arguments)) {
            throw new InvalidArgumentException(
                'You can not call a partial function please provide values for the following parameters: '. implode(', ' , $this->missingArguments)
            );
        }
        
        $arguments = $this->constructArguments(array_merge($arguments, $this->arguments));
        return $this->function->invokeArgs($arguments);
    }

    public function __invoke(array $arguments) {
        return $this->call($arguments);
    }
}
