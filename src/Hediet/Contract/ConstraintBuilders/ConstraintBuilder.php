<?php

namespace Hediet\Contract\ConstraintBuilders;

use Hediet\Contract\Expressions\ExpressionBuilder;
use PhpParser\Node\Expr;

abstract class ConstraintBuilder
{
    public abstract function getConstraint(Expr $expression, ExpressionBuilder $builder);
}

