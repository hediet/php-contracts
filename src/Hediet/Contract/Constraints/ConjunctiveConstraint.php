<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;
use Hediet\Contract\Helper\LanguageHelper;

class ConjunctiveConstraint extends AggregatedConstraint
{
    /**
     * @param EvaluationContext $context
     * @return string
     */
    public function getViolationMessage(EvaluationContext $context)
    {
        $parts = array();
        
        foreach ($this->getConstraints() as $c)
        {
            if ($c->isViolated($context))
            {
                $parts[] = $c->getViolationMessage($context);
            }
        }
        
        $result = LanguageHelper::joinSentences($parts);
        return $result;
    }

    public function isViolated(EvaluationContext $context)
    {
        foreach ($this->getConstraints() as $c)
        {
            if ($c->isViolated($context) !== false)
                return true;
        }
        return false;
    }
}