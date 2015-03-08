<?php
namespace Hediet\Contract\Expressions;

use Hediet\Contract\EvaluationContext;

class ConstantExpression extends Expression
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getHash()
    {
        return "constant_" . serialize($this->value);
    }

    public function evaluate(EvaluationContext $context)
    {
        return $this->value;
    }

    /**
     * @return VariableExpression[] indexed by their hash
     */
    public function getContainedVariables()
    {
        return array();
    }

    public function toString()
    {
        return (string)$this->value;
    }
}