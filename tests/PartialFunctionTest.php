<?php
declare(strict_types=1);

namespace Tests\Mamazu\PartialFunctions;

use Mamazu\PartialFunctions\PartialFunction;
use PHPUnit\Framework\TestCase;

final class PartialFunctionTest extends TestCase
{
    private function createPartialFunction(array $missingArguments): PartialFunction
    {
        return new PartialFunction(
            new \ReflectionFunction(
                static function () {
                }
            ), $missingArguments
        );
    }

    /** @test */
    public function it_detects_partial_functions(): void
    {
        $partialFunction = $this->createPartialFunction(['missing_argument']);

        self::assertTrue($partialFunction->isPartial());
    }


    /** @test */
    public function it_detects_nonpartial_functions(): void
    {
        $partialFunction = $this->createPartialFunction([]);

        self::assertFalse($partialFunction->isPartial());
    }

    /** @test */
    public function it_detects_non_partial_functions_if_additional_arguments_are_provided(): void
    {
        $partialFunction = $this->createPartialFunction(['argument']);

        self::assertFalse($partialFunction->isPartial(['argument' => 'test']));

        // But it does not apply the arguments and is still a partial function
        self::assertTrue($partialFunction->isPartial());
    }

    /**
     * @test
     * @dataProvider data_missingArguments
     */
    public function it_gets_a_list_of_missing_arguments(array $missingArguments): void
    {
        $partialFunction = $this->createPartialFunction($missingArguments);

        self::assertSame($partialFunction->getMissingArguments(), $missingArguments);
    }

    public function data_missingArguments(): array
    {
        return [
            'no arguments'         => [[]],
            'one missing argument' => [['missing_argument']],
        ];
    }

    /** @test */
    public function it_is_nolonger_a_partial_function_after_applying_them()
    {
        $partialFunction = new PartialFunction(
            new \ReflectionFunction(
                static function ($a) {
                    return $a;
                }
            ),
            ['a']
        );

        $partialFunction->apply(['a' => 'some function']);

        self::assertFalse($partialFunction->isPartial());
        self::assertSame($partialFunction(), 'some function');
    }

    /** @test */
    public function it_can_be_called_with_the_missing_arguments_supplied()
    {
        $partialFunction = new PartialFunction(
            new \ReflectionFunction(
                static function ($a) {
                    return $a;
                }
            ),
            ['a']
        );

        self::assertTrue($partialFunction->isPartial());
        self::assertSame($partialFunction->call(['a' => 'some function']), 'some function');
    }

    /** @test */
    public function it_throws_an_exception_on_calling_a_partial_function_with_missing_parameters()
    {
        self::expectExceptionMessage(
            'You can not call a partial function please provide values for the following parameters: argument'
        );

        $this->createPartialFunction(['argument'])->call();
    }

    /** @test */
    public function it_is_callable_like_a_normal_functions()
    {
        $partialFunction = new PartialFunction(
            new \ReflectionFunction(
                static function ($a = 1, $b) {
                    return $a + $b;
                }
            ),
            ['b']
        );
        
        self::assertSame($partialFunction(10), 11);
    }
}
