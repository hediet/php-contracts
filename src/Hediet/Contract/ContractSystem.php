<?php

namespace Hediet\Contract;

use Hediet\Contract\Constraints\Constraint;
use Hediet\Contract\Constraints\TypeConstraint;
use Hediet\Contract\Expressions\ParameterVariableExpression;
use Hediet\Contract\Helper\FindNodesInLineVisitor;
use Hediet\Types\Type;
use PhpParser\Lexer;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;

class ContractSystem
{
    public function throwException()
    {
        $expr = $this->getViolatedCondition();
        $constraint = $this->getConstraint($expr);

        $argsAndObject = $this->getArgsAndObject();
        $reflectionFunction = new \ReflectionMethod($argsAndObject["class"], $argsAndObject["function"]);
        $args = array();
        foreach ($reflectionFunction->getParameters() as $idx => $p)
            $args[$p->getName()] = $argsAndObject["args"][$idx];
        
        $context = new EvaluationContext($args, $argsAndObject["object"]);

        $message = $constraint->getViolationMessage($context);

        throw new \InvalidArgumentException($message);

    }


    /**
     * @param Expr $expression
     * @return Constraint
     */
    private function getConstraint(Expr $expression)
    {
        if ($expression instanceof Expr\FuncCall)
        {
            
            $typeChecks = array("array", "bool", "callable", "double", "float", "int", "integer", "null", 
                "object", "resource", "string");
            
            $functionName = $expression->name->toString();
            if (substr($functionName, 0, 3) === "is_")
            {
                $typeName = substr($functionName, 3);
                if (in_array($typeName, $typeChecks))
                {
                    $targetExpr = null;
                    $innerExpression = $expression->args[0]->value;

                    if ($innerExpression instanceof Expr\Variable)
                    {
                        $targetExpr = new ParameterVariableExpression($innerExpression->name);
                    }

                    return new TypeConstraint(Type::of($typeName), $targetExpr);
                }
            }
        }
    }


    /**
     * @return Expr
     */
    private function getViolatedCondition()
    {
        $position = $this->getViolatedConditionPosition();
        $file = $position["file"];
        $line = (int)$position["line"];
        $fileContent = file_get_contents($file);
        $lines = explode("\n", $fileContent);
        
        $visitor = new FindNodesInLineVisitor($line);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($visitor);

        $parser = new Parser(new Lexer());
        $parseResult = $parser->parse($fileContent);
        $traverser->traverse($parseResult);

        $foundItems = $visitor->getResult();

        $nodeDumper = new NodeDumper();


        /* @var $item StaticCall */
        $item = $foundItems[0];
        $arg = $item->args[0]->value;
        return $arg;
    }

    private function getArgsAndObject()
    {
        $trace = debug_backtrace();
        $methodCount = 0;
        $ignoredClasses = array(get_class($this), "Hediet\\Contract");
        while (isset($trace[$methodCount]["class"])
            && in_array($trace[$methodCount]["class"], $ignoredClasses)) {
            $methodCount++;
        }
        $methodCount--;
        $callersCallerStackFrame = null;
        if (isset($trace[$methodCount + 1]))
            $callersCallerStackFrame = $trace[$methodCount + 1];
        
        return $callersCallerStackFrame;
    }
    
    /**
     * @return array an array with keys "line" and "file"
     */
    private function getViolatedConditionPosition()
    {        
        $trace = debug_backtrace();
        $methodCount = 0;
        $ignoredClasses = array(get_class($this), "Hediet\\Contract");
        while (isset($trace[$methodCount]["class"])
            && in_array($trace[$methodCount]["class"], $ignoredClasses)) {
            $methodCount++;
        }
        $methodCount--;
        $callerStackFrame = $trace[$methodCount];
        return array("file" => $callerStackFrame["file"], "line" => $callerStackFrame["line"]);
    }
}