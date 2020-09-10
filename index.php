<?php

declare(strict_types=1);

class A
{
    public $name;
    public $flag;

    public function __construct(string $name, bool $flag = true, int $someNumber = 10)
    {
        $this->name = $name;
        $this->flag = $flag;
        $this->someNumber = $someNumber;
    }

    public function hello(string $a, string $b)
    {
        echo "A: $a, B: $b";
    }
}

class PartialFunction{
    private $callable;

    private $missingArguments;

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
        if($this->isPartial()) {
            throw new Exception('You can not call a partial function please provide values for the following parameters: '. implode(', ' , $this->missingArguments));
        }
        return Functor::call($this->callable, array_merge($this->arguments, $arguments));
    }

    public function __invoke(array $arguments) {
        return $this->call($arguments);
    }
}

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
            try {
                $parameter->getDefaultValue();
            }catch (ReflectionException $exception) {
                $requiredParameters[] = $parameter->getName();
            }
        }
        return $requiredParameters;

    }
}

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

function printDate($format = 'Y-m-d'){
    print_r(date($format));
}

$a = Functor::create(A::class, ['name' => 'Hello', 'someNumber' => 100]);

$helloMethod = new PartialFunction([$a, 'hello']);
$helloMethod
    ->apply(['b' => 'Boop'])
    ->apply(['a' => 'Boop2'])
    ->call();
die();

$p = new PartialFunction('printDate');
var_dump($p->isPartial());
