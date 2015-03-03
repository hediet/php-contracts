<?php
namespace Hediet\Contract\Expressions;

use Hediet\Contract\EvaluationContext;

class ThisVariableExpression extends VariableExpression
{
    public function __construct()
    {
        parent::__construct("this");
    }

    public function evaluate(EvaluationContext $context)
    {
        return $context->getValueOfThis();
    }
}