<?php
declare(strict_types=1);

namespace Tests\Mamazu\PartialFunctions;

use Mamazu\PartialFunctions\PartialFunctionFactory;
use PHPUnit\Framework\TestCase;

final class PartialFunctionFactoryTest extends TestCase
{
    /** @var PartialFunctionFactory */
    private $reflectionFactory;

    protected function setUp(): void
    {
        $this->reflectionFactory = new PartialFunctionFactory();
    }

    /** @test */
    public function it_creates_a_partial_function_for_internal_function(): void
    {
        $strpos = $this->reflectionFactory->createForCallable('strpos');
        
        self::assertSame($strpos->getMissingArguments(), ['haystack', 'needle']);
        self::assertTrue($strpos->isPartial());
    }
    
    /** @test */
    public function it_creates_a_partial_function_for_self_defined_function(): void
    {
        $someFunction = function (string $a= ''): string {
            return $a;
        };
        
        $ownFunction = $this->reflectionFactory->createForCallable($someFunction);

        self::assertSame($ownFunction->getMissingArguments(), []);
        self::assertFalse($ownFunction->isPartial());
    }

    /** @test */
    public function it_creates_a_partial_function_for_self_defined_method(): void
    {
        $object = new TestClass();
        $method = $this->reflectionFactory->createForCallable([$object, 'someFunction']);

        self::assertSame($method->getMissingArguments(), ['someParameter']);
        self::assertTrue($method->isPartial());
    }
    
    /** @test */
    public function it_creates_a_function_from_constructor(): void
    {
        $constructor = $this->reflectionFactory->createForCallable(TestClass::class.'::__construct');
        
        self::assertSame($constructor->getMissingArguments(), []);
        self::assertFalse($constructor->isPartial());
    }
}

class TestClass {
    public function __construct()
    {
    }

    public function someFunction(int $someParameter) {
        
    }
}
