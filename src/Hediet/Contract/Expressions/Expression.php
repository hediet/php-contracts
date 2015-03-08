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

    public abstract function getHash();

    public function equals(Expression $other)
    {
        return $this->getHash() === $other->getHash();
    }

    public abstract function evaluate(EvaluationContext $context);

    /**
     * @return VariableExpression[] indexed by their hash
     */
    public abstract function getContainedVariables();

    public abstract function toString();
    
    public function __toString()
    {
        return $this->toString();
    }
}