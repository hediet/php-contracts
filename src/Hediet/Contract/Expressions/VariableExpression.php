<?php
namespace Hediet\Contract\Expressions;

use Hediet\Contract\EvaluationContext;

class VariableExpression extends Expression
{
    /**
     * @var string
     */
    private $parameterName;

    /**
     * @param string $parameterName
     */
    public function __construct($parameterName)
    {
        $this->parameterName = $parameterName;
    }

    public function getName()
    {
        return $this->parameterName;
    }

    public function evaluate(EvaluationContext $context)
    {
        return null;
    }

    /**
     * @return VariableExpression[]
     */
    public function getContainedVariables()
    {
        return array($this->getHash() => $this);
    }

    public function toString()
    {
        return $this->getName();
    }

    public function getHash()
    {
        return '$' . $this->__toString();
    }

}