<?php

/**
 * @author Henning Dieterichs <henning.dieterichs@hediet.de>
 * @copyright (c) 2013-2014, Henning Dieterichs <henning.dieterichs@hediet.de>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Hediet;

use Hediet\Contract;
use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PHPUnit_Framework_TestCase;

class ContractTest extends PHPUnit_Framework_TestCase
{
    private function requireIsInt($i)
    {
        Contract::requires(is_int($i));
    }
    
    public function testIsInt()
    {
        $this->requireIsInt("test");
    }
    
    private function requireIsString($i)
    {
        Contract::requires(is_string($i));
    }
    
    public function testIsString()
    {
        $this->requireIsString(4);
    }

    public function test1()
    {
        $a = "test";
        $this->test2();
        //Contract::requires(false);
    }
    
    public function test3()
    {
        $code = '<? is_var($param);';

        $parser = new Parser(new Lexer());
        $nodeDumper = new NodeDumper;

        try {
            $stmts = $parser->parse($code);
            // $stmts is an array of statement nodes
            
            echo $nodeDumper->dump($stmts);
        } catch (Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }
        
        //print_r(get_defined_vars());
    }
    
    public function test2()
    {
        $code = file_get_contents("C:\Users\Henning\Documents\NetBeansProjects\PHP CodeContracts\src\Hediet\Contract.php");

        $parser = new Parser(new Lexer);

        try {
            $stmts = $parser->parse($code);
            // $stmts is an array of statement nodes
            
            print_r($stmts);
        } catch (Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }
        
        //print_r(get_defined_vars());
    }

}


class Test
{

    private $state;
    
    public function foo($i)
    {
        // InvalidArgumentException: Argument $i must be type of string, but was int.
        Contract::requires(is_string($i)); 
        
        // InvalidArgumentException: Argument $i must not be equal to $this->state, however $i was 4, $this->state was 5.
        Contract::requires($this->state != $i); 
        
        // InvariantViolationException: $this->state must not be null.
        Contract::requires($this->state != null);
    }

}
