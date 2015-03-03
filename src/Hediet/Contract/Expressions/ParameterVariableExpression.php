<?php

namespace Hediet\Contract\Expressions;

use Hediet\Contract\EvaluationContext;

class ParameterVariableExpression extends VariableExpression
{
    public function evaluate(EvaluationContext $context)
    {
        return $context->getValueOfArgument($this->getName());
    }
}

