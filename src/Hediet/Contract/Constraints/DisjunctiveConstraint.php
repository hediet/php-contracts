<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;

class DisjunctiveConstraint extends AggregatedConstraint
{
    /**
     * @param EvaluationContext $context
     * @return string
     */
    public function getViolationMessage(EvaluationContext $context)
    {
        return "Contract failed.";
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