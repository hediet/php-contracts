<?php

namespace Hediet\Contract\ConstraintBuilders;

use Hediet\Contract\Constraints\NumericRangeConstraint;
use Hediet\Contract\Expressions\ExpressionBuilder;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;

class NumericRangeConstraintBuilder extends ConstraintBuilder
{
    public function getConstraint(Expr $expression, ExpressionBuilder $builder)
    {
        if ($expression instanceof Greater || $expression instanceof GreaterOrEqual || $expression instanceof Smaller || $expression instanceof SmallerOrEqual)
        {
            $left = $builder->buildExpression($expression->left);
            $right = $builder->buildExpression($expression->right);

            $isGreater = ($expression instanceof Greater) || ($expression instanceof GreaterOrEqual);
            $isInclusive = ($expression instanceof GreaterOrEqual) || ($expression instanceof SmallerOrEqual);
            
            if (count($left->getContainedVariables()) === 0 && count($right->getContainedVariables()) > 0)
            {
                //swap "1 < $a" to "$a > 1"
                $l = $left;
                $left = $right;
                $right = $l;
                $isGreater = !$isGreater;
            }
            
            if ($isGreater)
                return new NumericRangeConstraint($left, $right, $isInclusive);
            else
                return new NumericRangeConstraint($left, null, false, $right, $isInclusive);
        }

        return null;
    }
}