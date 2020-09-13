# partial-function application in php
This library provides a partial function interface for php.

## Usage
```php
$factory = new PartialFunctionFactory();
$searchInString = $factory->createForCallable('strpos');
$searchInString->apply(['haystack' => 'Hello in PHP']);

$hellopos = $searchinstring->call(['needle' => 'hello']);
$phppos = $searchinstring->call(['needle' => 'php']);
```

