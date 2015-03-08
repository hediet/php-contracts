<?php

namespace Hediet\Contract\ConstraintBuilders;

use Hediet\Contract\Constraints\ConjunctiveConstraint;
use Hediet\Contract\Expressions\ExpressionBuilder;
use PhpParser\Node\Expr;

class ConjunctiveConstraintBuilder extends ConstraintBuilder
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
        if ($expression instanceof Expr\BinaryOp\BooleanAnd)
        {
            $leftConstraint = $this->constraintBuilder->getConstraint($expression->left, $builder);
            $rightConstraint = $this->constraintBuilder->getConstraint($expression->right, $builder);

            $result = array();

            if ($leftConstraint instanceof ConjunctiveConstraint)
                $result = array_merge($result, $leftConstraint->getConstraints());
            else
                $result[] = $leftConstraint;

            if ($rightConstraint instanceof ConjunctiveConstraint)
                $result = array_merge($result, $rightConstraint->getConstraints());
            else
                $result[] = $rightConstraint;

            return new ConjunctiveConstraint($result);
        }

        return null;
    }
}