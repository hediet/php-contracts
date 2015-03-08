<?php

namespace Hediet;

use Exception;
use Hediet\Contract;
use Hediet\Types\Type;
use PHPUnit_Framework_TestCase;

class ContractTest extends PHPUnit_Framework_TestCase
{

    private static $functionNumber = 0;

    private function fakeEval($phpCode, $callback)
    {
        $tmpfname = tempnam("/tmp", "fakeEval");
        $handle = fopen($tmpfname, "w+");
        fwrite($handle, "<?php\n" . $phpCode);
        fclose($handle);
        include $tmpfname;
        $callback();
        unlink($tmpfname);
        return get_defined_vars();
    }

    public function executeTest($requireExpression, $values, $expectedExceptionType, $message)
    {
        self::$functionNumber++;

        $args = array();
        foreach ($values as $idx => $value)
        {
            if (is_int($idx))
                $args[] = '$arg' . $idx;
            else
                $args[] = '$' . $idx;
        }
        $argStr = implode(", ", $args);
        $functionName = "contract_test" . self::$functionNumber;
        $code = "function " . $functionName . "(" . $argStr . ") {\n";
        $code .= "\Hediet\Contract::requires(" . $requireExpression . ");\n}";

        try
        {
            $this->fakeEval($code,
                    function() use ($functionName, $values)
            {
                call_user_func_array($functionName, $values);
            });
        }
        catch (Exception $e)
        {
            if (!$e instanceof $expectedExceptionType)
                throw $e;
            $this->assertEquals($message, $e->getMessage());
        }
    }

    private function executePrimitiveTypeAssertionTest($requireExpression, $args, $expectedType, $actualType)
    {
        if ($expectedType instanceof Type)
            $expectedType = $expectedType->getName();
        
        $this->executeTest($requireExpression, $args, "\InvalidArgumentException",
                "Argument 'arg0' must be of type '" . $expectedType 
                . "', but is of type '" . $actualType . "'.");
    }
    
    public function testPrimitiveTypeAssertions()
    {
        $this->executePrimitiveTypeAssertionTest('is_array($arg0)', array("string"), "array", "string");
        $this->executePrimitiveTypeAssertionTest('is_bool($arg0)', array("string"), "boolean", "string");
        $this->executePrimitiveTypeAssertionTest('is_callable($arg0)', array("string"), "callable", "string");
        $this->executePrimitiveTypeAssertionTest('is_double($arg0)', array("string"), "float", "string");
        $this->executePrimitiveTypeAssertionTest('is_float($arg0)', array("string"), "float", "string");
        $this->executePrimitiveTypeAssertionTest('is_real($arg0)', array("string"), "float", "string");
        $this->executePrimitiveTypeAssertionTest('is_int($arg0)', array("string"), "integer", "string");
        $this->executePrimitiveTypeAssertionTest('is_integer($arg0)', array("string"), "integer", "string");
        $this->executePrimitiveTypeAssertionTest('is_long($arg0)', array("string"), "integer", "string");
        $this->executePrimitiveTypeAssertionTest('is_null($arg0)', array("string"), "null", "string");
        $this->executePrimitiveTypeAssertionTest('is_object($arg0)', array("string"), "object", "string");
        $this->executePrimitiveTypeAssertionTest('is_resource($arg0)', array("string"), "resource", "string");
        $this->executePrimitiveTypeAssertionTest('is_string($arg0)', array(1), "string", "integer");
    }

    public function testOrTypeAssertion()
    {
        $this->executePrimitiveTypeAssertionTest('is_string($arg0) || is_int($arg0)', 
                array(null), Type::of("string|int"), "null");
        $this->executePrimitiveTypeAssertionTest('(is_string($arg0) || is_int($arg0)) || is_object($arg0)', 
                array(null), Type::of("string|int|object"), "null");
        $this->executePrimitiveTypeAssertionTest('is_string($arg0) || (is_int($arg0) || is_object($arg0))', 
                array(null), Type::of("string|int|object"), "null");
    }
    
    public function testNumericRange()
    {
        $this->executeTest('$arg0 <= 0', array(1), "\InvalidArgumentException", 
                "Argument 'arg0' must be less than or equal to '0', but is 1.");
        $this->executeTest('$arg0 < 1', array(1), "\InvalidArgumentException", 
                "Argument 'arg0' must be less than '1', but is 1.");
        $this->executeTest('$arg0 >= 2', array(1), "\InvalidArgumentException", 
                "Argument 'arg0' must be greater than or equal to '2', but is 1.");
        $this->executeTest('$arg0 > 1', array(1), "\InvalidArgumentException", 
                "Argument 'arg0' must be greater than '1', but is 1.");
        
        $this->executeTest('$arg0 > $arg1', array(1, 2), "\InvalidArgumentException", 
                "Argument 'arg0' must be greater than argument 'arg1', but 'arg0' is 1 and 'arg1' is 2.");
        
    }
    
    public function t2estArrayTest()
    {
        $this->executeTest('\Hediet\Contract::requires(\Hediet\Contract\Helper::isArray($arg0, "string"))',
                array("string"), "string[]", "string");
    }

}

class Test
{

    private $state;

    public function foo($i)
    {

        // InvalidArgumentException: Argument $i must not be equal to $this->state, however $i was 4 and $this->state was 5.
        Contract::requires($this->state != $i);

        // InvariantViolationException: $this->state must not be null.
        Contract::requires($this->state != null);
    }

}
