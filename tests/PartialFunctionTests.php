<?php
declare(strict_types=1);

namespace Tests\Mamazu\PartialFunctions;

use Mamazu\PartialFunctions\PartialFunction;
use PHPUnit\Framework\TestCase;

class PartialFunctionTests extends TestCase
{
    public function it_detects_partial_functions(callable $function): void
    {
        $partialFunction = new PartialFunction(new \ReflectionFunction($function), []);
        
        self::assertTrue($partialFunction->isPartial());
    }
}
