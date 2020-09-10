<?php

declare(strict_types=1);

namespace Mamazu\PartialFunctions;

use ReflectionMethod;

class ReflectionFactory {
    /**
     * @param callable $callable
     *
     * @return ReflectionFunction|ReflectionMethod
     */
    public function reflect(callable $callable) {
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

    public function getRequiredParamters(callable $callable):array {
        $requiredParameters = [];
        foreach($this->reflect($callable)->getParameters() as $parameter) {
            if(!$parameter->isOptional()) {
                continue;
            }

            $requiredParameters[] = $parameter->getName();
        }
        return $requiredParameters;

    }
}
