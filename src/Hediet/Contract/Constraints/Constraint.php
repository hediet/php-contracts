<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;

abstract class Constraint
{
    /**
     * @param EvaluationContext $context
     * @return string
     */
    public abstract function getViolationMessage(EvaluationContext $context);
}