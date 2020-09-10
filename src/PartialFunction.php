<?php

declare(strict_types=1);

namespace Mamazu\PartialFunctions;

class PartialFunction {
    /** @var callable */
    private $callable;

    /** @var array<string> */
    private $missingArguments;

    /** @var array<mixed> */
    private $arguments = [];

    public function __construct(callable $callable) {
        $this->callable = $callable;
        $this->missingArguments = (new ReflectionFactory())->getRequiredParamters($callable);
    }

    public function isPartial(): bool
    {
        return count($this->missingArguments) > 0;
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

    public function call(array $arguments = []) {
        if ($this->isPartial()) {
            throw new Exception('You can not call a partial function please provide values for the following parameters: '. implode(', ' , $this->missingArguments));
        }
        return Functor::call($this->callable, array_merge($this->arguments, $arguments));
    }

    public function __invoke(array $arguments) {
        return $this->call($arguments);
    }
}
