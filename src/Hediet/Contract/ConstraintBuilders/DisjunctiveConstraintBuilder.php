<?php

namespace Hediet\Contract\ConstraintBuilders;

use Hediet\Contract\Constraints\DisjunctiveConstraint;
use Hediet\Contract\Expressions\ExpressionBuilder;
use PhpParser\Node\Expr;

class DisjunctiveConstraintBuilder extends ConstraintBuilder
{
    /**
     * @var ConstraintBuilder
     */
    private $constraintBuilder;

    public function __construct(ConstraintBuilder $constraintBuilder)
    {
        $this->constraintBuilder = $constraintBuilder;
    }

    public function getConstraint(Expr $expression, ExpressionBuilder $builder)
    {
        if ($expression instanceof Expr\BinaryOp\BooleanOr)
        {
            $leftConstraint = $this->constraintBuilder->getConstraint($expression->left, $builder);
            $rightConstraint = $this->constraintBuilder->getConstraint($expression->right, $builder);

            $result = array();

            if ($leftConstraint instanceof DisjunctiveConstraint)
                $result = array_merge($result, $leftConstraint->getConstraints());
            else
                $result[] = $leftConstraint;

            if ($rightConstraint instanceof DisjunctiveConstraint)
                $result = array_merge($result, $rightConstraint->getConstraints());
            else
                $result[] = $rightConstraint;

            return new DisjunctiveConstraint($result);
        }

        return null;
    }
}