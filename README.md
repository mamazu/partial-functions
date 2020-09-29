# partial-function application in php
This library provides a partial function interface for php.

## Usage
The simpletest way to use it with the FunctionInvoker:
```php
function f(string $greeting = 'Hello', string $name = 'you') {
    return $greeting.', '.$name;
}

echo \Mamazu\PartialFunctions\FunctionInvoker::invoke('f', ['name' => 'Anonymous']);

// Will echo "Hello, Anonymous"
```

### Object oriented
```php
$factory = new \Mamazu\PartialFunctions\PartialFunctionFactory();
$searchInString = $factory->createForCallable('strpos');
$searchInString->apply(['haystack' => 'Hello in PHP']);

$hellopos = $searchInString->call(['needle' => 'hello']);
$phppos = $searchInString->call(['needle' => 'php']);
```

