<?php

namespace Hediet\Contract\ConstraintBuilders;

use Hediet\Contract\Constraints\TypeConstraint;
use Hediet\Contract\Expressions\ExpressionBuilder;
use Hediet\Types\Type;
use PhpParser\Node\Expr;

class TypeConstraintBuilder extends ConstraintBuilder
{
    public function getConstraint(Expr $expression, ExpressionBuilder $builder)
    {
        if ($expression instanceof Expr\BinaryOp\Identical)
        {
            $right = $expression->right;
            $left = $expression->left;
            
            if (!($right instanceof Expr\ConstFetch))
            {
                $right = $left;
                $left = $expression->right;
            }
            
            if ($right instanceof Expr\ConstFetch)
            {
                if (strtolower($right->name->toString()) === "null")
                    return new TypeConstraint(Type::ofNull(), $builder->buildExpression($left));
            }
        }
        else if ($expression instanceof Expr\FuncCall)
        {
            $typeChecks = array("array", "bool", "callable", "double", "float", "real", "int", "integer", "long", "null",
                "object", "resource", "string");

            $functionName = $expression->name->toString();
            if (substr($functionName, 0, 3) === "is_")
            {
                $typeName = substr($functionName, 3);
                if (in_array($typeName, $typeChecks))
                {
                    $targetExpr = null;
                    $innerExpression = $expression->args[0]->value;

                    $targetExpr = $builder->buildExpression($innerExpression);

                    return new TypeConstraint(Type::of($typeName), $targetExpr);
                }
            }
        }

        return null;
    }
}
