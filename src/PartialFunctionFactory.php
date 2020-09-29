<?php

declare(strict_types=1);

namespace Mamazu\PartialFunctions;

use ReflectionMethod;
use Closure;
use ReflectionFunction;
use ReflectionFunctionAbstract;

class PartialFunctionFactory 
{
    /**
     * @param callable $callable
     *
     * @return ReflectionFunction|ReflectionMethod
     */
    private function reflect($callable) {
        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        }
        if (is_string($callable)) {
            $pcs = explode('::', $callable);
            return count($pcs) > 1 ? new ReflectionMethod($pcs[0], $pcs[1]) : new ReflectionFunction($callable);
        }
        if (!is_array($callable)) {
            $callable = [$callable, '__invoke'];
        }
        return new ReflectionMethod($callable[0], $callable[1]);
    }

    private function getMissingParameters(ReflectionFunctionAbstract $reflection):array {
        $requiredParameters = [];
        foreach($reflection->getParameters() as $parameter) {
            if($parameter->isOptional()) {
                continue;
            }

            $requiredParameters[] = $parameter->getName();
        }
        return $requiredParameters;
    }

    public function createForCallable($callable): PartialFunction
    {
        $reflection = $this->reflect($callable);
        return new PartialFunction(
            $reflection,
            $this->getMissingParameters($reflection)
        );
    }

}
