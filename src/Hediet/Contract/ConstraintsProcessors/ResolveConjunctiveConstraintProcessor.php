<?php
namespace Hediet\Contract\ConstraintsProcessors;

use Hediet\Contract\Constraints\ConjunctiveConstraint;

class ResolveConjunctiveConstraintProcessor extends ConstraintsProcessor
{
    public function processConstraints(array $constraints)
    {
        $result = array();

        foreach ($constraints as $constraint)
        {
            if ($constraint instanceof ConjunctiveConstraint)
            {
                foreach ($constraint->getConstraints() as $c)
                {
                    $result[] = $c;
                }
            }
            else
            {
                $result[] = $constraint;
            }
        }

        return $result;
    }
}