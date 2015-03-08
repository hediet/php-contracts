<?php

namespace Hediet\Contract\ConstraintsProcessors;

use Hediet\Contract\Constraints\Constraint;

abstract class ConstraintsProcessor
{
    /**
     * @param Constraint[] $constraints
     * @return Constraint[]
     */
    public abstract function processConstraints(array $constraints);
}

