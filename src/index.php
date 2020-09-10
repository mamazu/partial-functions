<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Mamazu\PartialFunctions\Functor;
use Mamazu\PartialFunctions\PartialFunction;

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

$a = Functor::create(A::class, ['name' => 'Hello', 'someNumber' => 100]);

$helloMethod = new PartialFunction([$a, 'hello']);
$helloMethod
    ->apply(['b' => 'Boop'])
    ->apply(['a' => 'Boop2'])
    ->call();
die();

$p = new PartialFunction('printDate');
var_dump($p->isPartial());
