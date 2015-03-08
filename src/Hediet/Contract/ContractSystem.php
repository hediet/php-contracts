<?php

namespace Hediet\Contract;

use Hediet\Contract\ConstraintBuilders\ComposedConstraintBuilder;
use Hediet\Contract\ConstraintBuilders\DisjunctiveConstraintBuilder;
use Hediet\Contract\ConstraintBuilders\NumericRangeConstraintBuilder;
use Hediet\Contract\ConstraintBuilders\TypeConstraintBuilder;
use Hediet\Contract\Constraints\ConjunctiveConstraint;
use Hediet\Contract\Constraints\Constraint;
use Hediet\Contract\ConstraintsProcessors\ComposedConstraintsProcessor;
use Hediet\Contract\ConstraintsProcessors\DisjunctiveTypeConstraintProcessor;
use Hediet\Contract\Expressions\ExpressionBuilder;
use Hediet\Contract\Helper\FindNodesInLineVisitor;
use ImagickException;
use InvalidArgumentException;
use Nunzion\StackTrace\CallFrames\InstanceMethodCallFrame;
use Nunzion\StackTrace\StackTrace;
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

        if ($constraint === null)
        {
            $message = "Contract failed.";
        }
        else
        {
            $trace = $this->getFilteredStackTrace();
            $frame = $trace->getCallFrame(1);
            $target = ($frame instanceof InstanceMethodCallFrame) 
                    ? $frame->getTargetObject() : null;

            $context = new EvaluationContext($frame->getArgumentsByName(), $target);

            $message = $constraint->getViolationMessage($context);
        }
        
        throw new InvalidArgumentException($message);
    }


    /**
     * @param Expr $expression
     * @return Constraint
     */
    private function getConstraint(Expr $expression)
    {
        $constraintBuilder = new ComposedConstraintBuilder();

        $constraintBuilder->addConstraintBuilder(new TypeConstraintBuilder());
        $constraintBuilder->addConstraintBuilder(new NumericRangeConstraintBuilder());
        $constraintBuilder->addConstraintBuilder(new DisjunctiveConstraintBuilder($constraintBuilder));

        $expressionBuilder = new ExpressionBuilder();
        $constraint = $constraintBuilder->getConstraint($expression, $expressionBuilder);

        $constraints = array($constraint);

        $processors = array();
        $processors[] = new DisjunctiveTypeConstraintProcessor();
        $processor = new ComposedConstraintsProcessor($processors);

        $constraints = $processor->processConstraints($constraints);

        if (count($constraints) === 1)
            return $constraints[0];
        else
            return new ConjunctiveConstraint($constraints);
    }


    private function getFilteredStackTrace()
    {
        $trace = StackTrace::create()
                ->excludeCallsFromNamespace("Hediet\\Contract")
                ->excludeCallsFromClass("Hediet\\Contract")
                ->resolveArgumentNames();
        return $trace;
    }
    
    /**
     * @return Expr
     */
    private function getViolatedCondition()
    {
        $trace = $this->getFilteredStackTrace();
        $frame = $trace->getCallFrame(0);

        $visitor = new FindNodesInLineVisitor($frame->getLine());
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($visitor);

        $parser = new Parser(new Lexer());
        $parseResult = $parser->parse($frame->getSource()->getContent());
        $traverser->traverse($parseResult);

        $foundItems = $visitor->getResult();

        $nodeDumper = new NodeDumper();

        /* @var $item StaticCall */
        $item = $foundItems[0];
        $arg = $item->args[0]->value;
        
        return $arg;
    }
}