<?php

namespace Hediet\Contract\ConstraintsProcessors;

use Hediet\Contract\Constraints\Constraint;

class ComposedConstraintsProcessor extends ConstraintsProcessor
{
    /**
     * @var array|ConstraintsProcessor[]
     */
    private $processors;

    /**
     * @param ConstraintsProcessor[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * @param Constraint[] $constraints
     * @return Constraint[]
     */
    public function processConstraints(array $constraints)
    {
        $source = $constraints;
        foreach ($this->processors as $p) {
            $source = $p->processConstraints($source);
        }
        return $source;
    }
}