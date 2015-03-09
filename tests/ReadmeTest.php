<?php

namespace Hediet;

use Hediet\Contract;
use PHPUnit_Framework_TestCase;

class ReadmeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 'b' must be of type 'integer', but is of type 'string'.
     */
    public function testReadme()
    {
        sum(1, "test");
    }
    
    
    public function intArgumentsProvider()
    {
        return array(array(7, 1));
    }
    
    /**
     * @dataProvider intArgumentsProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 'a' must be greater than '1' and less than or equal to argument 'b', but 'a' is 7 and 'b' is 1.
     */
    public function testExampleRange($a, $b)
    {
        Contract::requires(1 < $a && $a <= $b);
    }
    
    /**
     * @dataProvider intArgumentsProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 'a' must be of type 'null|string', but is of type 'integer'.
     */
    public function testExampleUnionType($a)
    {
        Contract::requires($a === null || is_string($a));
    }
    
    /**
     * @dataProvider intArgumentsProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 'a' must be greater than '10', 
     * but is 7 or argument 'a' must be of type 'null', but is of type 'integer'.
     */
    public function testExample4($a)
    {
        Contract::requires(($a === null) || ($a > 10));
    }
   
}

function sum($a, $b)
{
    Contract::requires(is_int($a) && is_int($b));
}