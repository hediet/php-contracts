<?php

namespace Hediet;

use Hediet\Contract\ContractSystem;
use PhpParser\Node\Expr;
use UnexpectedValueException;

class Contract
{
    public static function requires($condition, $args = array())
    {
        if ($condition)
            return;
        
        self::throwException();
    }
    
    
    public static function throwException()
    {
        $c = new ContractSystem();
        $c->throwException();
        throw new UnexpectedValueException();
    }
    
}





class PrimitiveTypeConstraintAnalyzer
{
    public function processStatement(Expr $expression)
    {
        $str = 'is_string($param);';
        
    }
}



/*
class ContractReflector
{
    public function getConstraints($method);
}




class ArrayConstraint
{
    private $elementConstraint;
}


class StringLengthConstraint
{
    
}

class NumericRangeConstraint
{
    
}
 
 */