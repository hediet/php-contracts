<?php

namespace Hediet\Contract\ConstraintsProcessors;

use Hediet\Contract\Constraints\DisjunctiveConstraint;
use Hediet\Contract\Constraints\TypeConstraint;
use Hediet\Types\Type;

class DisjunctiveTypeConstraintProcessor extends ConstraintsProcessor
{
    public function processConstraints(array $constraints)
    {
        $result = array();
        foreach ($constraints as $constraint)
        {
            if (!($constraint instanceof DisjunctiveConstraint))
            {
                $result[] = $constraint;
                continue;
            }

            /* @var Type[][string] $typesForExpressions */
            $typesForExpressions = array();

            /* @var Expression[string] $expressionsByString */
            $expressionsByString = array();
            
            $newConstraints = array();

            foreach ($constraint->getConstraints() as $subConstraint)
            {
                if ($subConstraint instanceof TypeConstraint)
                {
                    $exprKey = $subConstraint->getTarget()->getHash();
                    if (!isset($typesForExpressions[$exprKey]))
                    {
                        $typesForExpressions[$exprKey] = array();
                        $expressionsByString[$exprKey] = $subConstraint->getTarget();
                    }
                    
                    $types = &$typesForExpressions[$exprKey];

                    $types[] = $subConstraint->getRequiredType();
                }
                else
                    $newConstraints[] = $subConstraint;
            }

            foreach ($typesForExpressions as $exprKey => $unitedType)
            {
                $newConstraints[] = new TypeConstraint(Type::ofUnion($unitedType),
                        $expressionsByString[$exprKey]);
            }

            if (count($newConstraints) === 1)
            {
                $result[] = $newConstraints[0];
            }
            else
            {
                $result[] = new DisjunctiveConstraint($newConstraints);
            }
        }

        return $result;
    }
}