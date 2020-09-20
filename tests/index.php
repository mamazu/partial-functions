<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Mamazu\PartialFunctions\PartialFunctionFactory;

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

function printDate($format = 'Y-m-d'){
    print_r(date($format));
}

$factory = new PartialFunctionFactory();

$searchInString = $factory->createForCallable('strpos');
$searchInString->apply(['haystack' => 'Hello in PHP']);


var_dump($searchInString->call(['needle' => 'Hello']));
var_dump($searchInString->call(['needle' => 'PHP']));
var_dump($searchInString('PHP'));

