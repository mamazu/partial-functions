<?php


namespace Mamazu\PartialFunctions;


class FunctionInvoker
{
    static function invoke($callable, array $arguments) {
        return (new PartialFunctionFactory())->createForCallable($callable)->call($arguments);
    }
}
