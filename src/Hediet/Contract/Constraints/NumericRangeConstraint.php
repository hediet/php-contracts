<?php

namespace Hediet\Contract\Constraints;

use Hediet\Contract\EvaluationContext;
use Hediet\Contract\Expressions\Expression;
use Hediet\Contract\Expressions\ParameterVariableExpression;
use Hediet\Contract\Expressions\VariableExpression;

class NumericRangeConstraint extends Constraint
{
    private $min;
    private $minInclusive;
    private $max;
    private $maxInclusive;
    /**
     * @var Expression
     */
    private $target;

    /**
     * Either $min or $max must not be null.
     *
     * @param Expression $target
     * @param Expression|null $min
     * @param boolean $minInclusive
     * @param Expression|null $max
     * @param boolean $maxInclusive
     */
    function __construct(Expression $target, Expression $min = null, $minInclusive = false,
                         Expression $max = null, $maxInclusive = false)
    {
        if ($min === null && $max === null)
            throw new \InvalidArgumentException("Either min or max must not be null.");

        $this->min = $min;
        $this->minInclusive = $minInclusive;
        $this->max = $max;
        $this->maxInclusive = $maxInclusive;
        $this->target = $target;
    }

    /**
     * @return Expression
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return Expression|null
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return bool
     */
    public function getMinInclusive()
    {
        return $this->minInclusive;
    }

    /**
     * @return Expression|null
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return boolean
     */
    public function getMaxInclusive()
    {
        return $this->maxInclusive;
    }

    /**
     * @param EvaluationContext $context
     * @return string
     */
    public function getViolationMessage(EvaluationContext $context)
    {
        // Contract::require($a >= 4 && $a < 10);
        // Argument 'a' must be greater than or equal to 4 and less than 10, but is 2.

        // Contract::require($a >= $b);
        // Argument 'a' must be greater than or equal to argument 'b', but 'a' is 2 and 'b' is 4.

        // Contract::require($a >= $b + 1);
        // For argument 'a' and 'b', 'a' must be greater than or equal to 'b + 1', but 'a' is 2 and 'b + 1' is 4.

        // Contract::require(count($a) > 0);
        // For argument 'a', 'count($a)' must be greater than 0, but is 0.

        $butIs = "";
        $actualValue = $this->getTarget()->evaluate($context);

        if (Expression::hasValue($actualValue))
        {
            $butIs = ", but is '$actualValue'";
        }

        $start = "";
        if ($this->target instanceof ParameterVariableExpression)
        {
            $start = "Argument '" . $this->target->getName() . "'";
        }

        $body = $start . " must be ";

        $values = array();

        $val = $this->target->evaluate($context);
        if (Expression::hasValue($val))
        {
            $values[$this->target->__toString()] = $val;
        }

        $needsAnd = false;
        if ($this->min !== null)
        {
            $minVal = $this->min->evaluate($context);
            if (Expression::hasValue($minVal))
            {
                if ($this->min->__toString() != $minVal)
                    $values[$this->min->__toString()] = $minVal;
            }

            $body .= "greater than ";
            if ($this->minInclusive)
                $body .= "or equal to ";

            $body .= "'" . $this->min->__toString() . "'";
            $needsAnd = true;
        }
        if ($this->max !== null)
        {
            $maxVal = $this->max->evaluate($context);
            if (Expression::hasValue($maxVal))
            {
                if ($this->max->__toString() != $maxVal)
                    $values[$this->max->__toString()] = $maxVal;
            }

            if ($needsAnd)
                $body .= " and";

            $body .= "less than ";
            if ($this->maxInclusive)
                $body .= "or equal to ";

            $body .= "'" . $this->max->__toString() . "'";
        }

        if (count($values) > 0)
        {
            $body .= ", but";
            $keys = array_keys($values);
            if ($keys[0] === $this->target->__toString() && count($values) === 1)
            {
                $body .= " is " . $values[$keys[0]];
            }
            else
            {
                $i = count($values);
                foreach ($values as $key => $value)
                {
                    $i--;
                    if ($i != count($values) - 1)
                    {
                        if ($i == 0)
                            $body .= " and";
                        else
                            $body .= ",";
                    }
                    $body .= " '" . $key . "' is " . $value;
                }
            }
        }

        $body .= ".";

        return $body;
    }

    /**
     * @param EvaluationContext $context
     * @return boolean|null
     */
    public function isViolated(EvaluationContext $context)
    {
        $result = false;
        $val = $this->getTarget()->evaluate($context);

        if (!Expression::hasValue($val))
            return null;

        if ($this->min !== null)
        {
            $minVal = $this->min->evaluate($context);
            if (!Expression::hasValue($minVal))
            {
                $result = null;
            }
            else
            {
                if ($val < $minVal) return true;

                if (!$this->minInclusive && $val <= $minVal)
                    return true;
            }
        }
        if ($this->max !== null)
        {
            $maxVal = $this->min->evaluate($context);
            if (!Expression::hasValue($maxVal))
            {
                $result = null;
            }
            else
            {
                if ($val > $maxVal) return true;

                if (!$this->maxInclusive && $val >= $maxVal)
                    return true;
            }
        }

        return $result;
    }

    /**
     * @return VariableExpression[] indexed by hash
     */
    public function getReferencedVariables()
    {
        $result = array();
        if ($this->min !== null)
            $result = array_merge($result, $this->min->getContainedVariables());
        if ($this->max !== null)
            $result = array_merge($result, $this->max->getContainedVariables());
        $result = array_merge($result, $this->target->getContainedVariables());
        return $result;
    }
}