<?php
declare(strict_types=1);

namespace Tests\Mamazu\PartialFunctions;

use Mamazu\PartialFunctions\FunctionInvoker;
use PHPUnit\Framework\TestCase;

class FunctionInvokerTest extends TestCase
{
    /** @test */
    public function it_can_invoke_a_function() 
    {
        $output = FunctionInvoker::invoke(
            'str_getcsv', 
            ['string' => 'hello,test,|a,b|', 'enclosure' => '|']
        );
        
        self::assertSame($output, ['hello', 'test', 'a,b']);
    }
}
