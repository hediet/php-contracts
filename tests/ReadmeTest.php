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
    
}

function sum($a, $b)
{
    Contract::requires(count($a) === 0);
}
