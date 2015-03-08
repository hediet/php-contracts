<?php

namespace Hediet\Contract\Expressions;

use PhpParser\Node\Expr;

class ExpressionBuilder
{
    /**
     * @return Expression
     */
    public function buildExpression(Expr $expr)
    {
        if ($expr instanceof Expr\Variable)
        {
            return new ParameterVariableExpression($expr->name);
        }
        if ($expr instanceof \PhpParser\Node\Scalar\LNumber)
        {
            return new ConstantExpression($expr->value);
        }
        
        return null;
    }
}
