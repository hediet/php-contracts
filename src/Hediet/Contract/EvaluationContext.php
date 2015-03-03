<?php

namespace Hediet\Contract;

class EvaluationContext
{

    /**
     * @var array
     */
    private $passedValues;
    private $object;

    /**
     * @var mixed[string]
     */
    private $methodArgs;

    public function __construct(array $methodArgs, $object, array $passedValues = array())
    {
        $this->methodArgs = $methodArgs;
        $this->object = $object;
        $this->passedValues = $passedValues;
    }

    /**
     * @param string $argumentName
     * @return mixed
     */
    public function getValueOfArgument($argumentName)
    {
        return $this->methodArgs[$argumentName];
    }

    /**
     * @return mixed
     */
    public function getValueOfThis()
    {
        return $this->object;
    }
}