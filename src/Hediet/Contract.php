<?php

namespace Hediet;

use Hediet\Contract\ContractSystem;
use PhpParser\Node\Expr;
use UnexpectedValueException;

class Contract
{
    /**
     * Throws an appropriate exception if the condition is false.
     * 
     * @param type $condition
     * @param type $args
     * @return type
     */
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



 */