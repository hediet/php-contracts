<?php

namespace Hediet\Contract\Constraints;

abstract class AggregatedConstraint extends Constraint
{
    /**
     * @var Constraint[]
     */
    private $constraints;

    public function __construct(array $constraints)
    {
        $this->constraints = $constraints;
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @return VariableExpression[] indexed by string
     */
    public function getReferencedVariables()
    {
        $result = array();
        foreach ($this->constraints as $c)
        {
            $result = array_merge($result, $c->getReferencedVariables());
        }
        return $result;
    }
}