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

            if ($expression instanceof Greater) // left > right
            {
                return new NumericRangeConstraint($left, $right, false);
            }
            else if ($expression instanceof GreaterOrEqual) // left >= right
            {
                return new NumericRangeConstraint($left, $right, true);
            }
            else if ($expression instanceof Smaller) // left < right
            {
                return new NumericRangeConstraint($left, null, false, $right, false);
            }
            else if ($expression instanceof SmallerOrEqual) // left <= right
            {
                return new NumericRangeConstraint($left, null, false, $right, true);
            }
        }

        return null;
    }
}