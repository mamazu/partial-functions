# partial-function application in php
This library provides a partial function interface for php.

## Usage
```php
$searchInString = new PartialFunction('strpos');
$searchInString->apply(['haystack' => 'Hello in PHP']);

$helloPos = $searchInString->call(['needle' => 'Hello']);
$phpPos = $searchInString->call(['needle' => 'PHP']);
```

