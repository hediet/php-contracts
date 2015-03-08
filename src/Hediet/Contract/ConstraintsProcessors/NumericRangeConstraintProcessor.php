<?php

namespace Hediet\Contract\ConstraintsProcessors;

use Hediet\Contract\Constraints\NumericRangeConstraint;

class NumericRangeConstraintProcessor extends ConstraintsProcessor
{
    /**
     * @param Constraint[] $constraints
     * @return Constraint[]
     */
    public function processConstraints(array $constraints)
    {
        /* @var NumericRangeConstraint[][string] */
        $numericRangeConstraints = array();
        
        $result = array();
        foreach ($constraints as $c)
        {
            if ($c instanceof NumericRangeConstraint)
            {
                if ($c->getMax() === null || $c->getMin() === null)
                {
                    $hash = $c->getTarget()->getHash();
                    if (!isset($numericRangeConstraints[$hash]))
                    {
                        $numericRangeConstraints[$hash] = array();
                    }
                    $arr = &$numericRangeConstraints[$hash];
                    $arr[] = $c;
                    continue;
                }
            }
            
            $result[] = $c;
        }
        
        foreach ($numericRangeConstraints as $cs)
        {
            $min = null;
            $max = null;
            
            /* @var $c NumericRangeConstraint */
            foreach ($cs as $c)
            {
                if ($c->getMax() === null && $min === null)
                {
                    $min = $c;
                }
                else if ($c->getMin() === null && $max === null)
                {
                    $max = $c;
                }
                else
                    $result[] = $c;
            }

            if ($min !== null && $max !== null)
            {
                $result[] = new NumericRangeConstraint($min->getTarget(), 
                    $min->getMin(), $min->getMinInclusive(),
                    $max->getMax(), $max->getMaxInclusive());
            }
            else
            {
                if ($min !== null)
                    $result[] = $min;
                if ($max !== null)
                    $result[] = $max;
            }
        }
        
        return $result;
    }
}
