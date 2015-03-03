<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;
use Hediet\Contract\Expressions\Expression;
use Hediet\Contract\Expressions\ParameterVariableExpression;
use Hediet\Types\Type;

class TypeConstraint extends Constraint
{
    /**
     * @var Type
     */
    private $requiredType;

    /**
     * @var \Hediet\Contract\Expressions\Expression
     */
    private $target;

    public function __construct(Type $requiredType, Expression $target)
    {
        $this->requiredType = $requiredType;
        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return Type
     */
    public function getRequiredType()
    {
        return $this->requiredType;
    }


    public function getViolationMessage(EvaluationContext $context)
    {
        $butIs = "";
        $actualValue = $this->getTarget()->evaluate($context);
        if (Expression::hasValue($actualValue)) {
            $actualType = Type::byValue($actualValue);

            $butIs = ", but is of type '{$actualType->getName()}'";
        }

        $start = "";
        if ($this->target instanceof ParameterVariableExpression)
        {
            $start = "Argument '" . $this->target->getName() . "'";
        }

        $body = $start . " must be of type '" . $this->getRequiredType()->getName() . "'" . $butIs . ".";

        return $body;

        //  Argument 'a' must be of type '{expected}', but is of type '{actual}'.
        //  For Argument 'a', the value of '$a->getName()' must be of type '{expected}'.
    }
}