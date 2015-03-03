<?php

namespace Hediet\Contract\Expressions;

use Hediet\Contract\EvaluationContext;

abstract class Expression
{

    private static $noValue;

    public static function getNoValue()
    {
        if (self::$noValue === null)
            self::$noValue = new \stdClass();
        
        return self::$noValue;
    }

    public static function hasValue($value)
    {
        return $value !== self::getNoValue();
    }
    
    public abstract function evaluate(EvaluationContext $context);

    /**
     * @return VariableExpression[]
     */
    public abstract function getVariableExpressions();

    public abstract function __toString();
}
