<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;
use Hediet\Contract\Expressions\VariableExpression;

abstract class Constraint
{
    /**
     * @param EvaluationContext $context
     * @return string
     */
    public abstract function getViolationMessage(EvaluationContext $context);

    /**
     * @param EvaluationContext $context
     * @return boolean|null
     */
    public abstract function isViolated(EvaluationContext $context);

    /**
     * @return VariableExpression[] indexed by hash
     */
    public abstract function getReferencedVariables();

    public function containsVariable(VariableExpression $variable)
    {
        $referencedVariables = $this->getReferencedVariables();
        return isset($referencedVariables[$variable->getHash()]);
    }
}