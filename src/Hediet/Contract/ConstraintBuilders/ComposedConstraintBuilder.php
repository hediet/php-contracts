<?php

namespace Hediet\Contract\ConstraintBuilders;

use Hediet\Contract\Expressions\ExpressionBuilder;
use PhpParser\Node\Expr;

class ComposedConstraintBuilder extends ConstraintBuilder
{
    /**
     * @var ConstraintBuilder[]
     */
    private $constraintBuilders;

    /**
     * @param ConstraintBuilder[] $constraintBuilders
     */
    public function __construct(array $constraintBuilders = null)
    {
        if ($constraintBuilders === null)
            $constraintBuilders = array();
        $this->constraintBuilders = $constraintBuilders;
    }

    public function addConstraintBuilder(ConstraintBuilder $builder)
    {
        $this->constraintBuilders[] = $builder;
    }

    public function getConstraint(Expr $expression, ExpressionBuilder $builder)
    {
        foreach ($this->constraintBuilders as $constraintBuilder) {
            $result = $constraintBuilder->getConstraint($expression, $builder);
            if ($result !== null)
                return $result;
        }
        return null;
    }
}

