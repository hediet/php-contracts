<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;
use Hediet\Contract\Expressions\VariableExpression;

class DisjunctiveConstraint extends AggregatedConstraint
{
    /**
     * @param EvaluationContext $context
     * @return string
     */
    public function getViolationMessage(EvaluationContext $context)
    {
        // TODO: Implement getViolationMessage() method.
    }

    public function isViolated(EvaluationContext $context)
    {
        foreach ($this->getConstraints() as $c)
        {
            if ($c->isViolated($context) === false)
                return false;
        }
        return true;
    }
}