<?php

namespace Hediet\Contract\Helper;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FindNodesInLineVisitor extends NodeVisitorAbstract
{
    private $line;
    private $result = array();
    private $lastNode = null;
    
    public function __construct($line)
    {
        $this->line = $line;
    }
    
    public function leaveNode(Node $node) 
    {
        if ($this->lastNode === $node)
            $this->lastNode = null;
    }
    
    public function enterNode(Node $node)
    {
        if ($this->lastNode !== null)
            return;
        
        if ($node->getLine() === $this->line)
        {
            $this->lastNode = $node;
            $this->result[] = $node;
        }
    }
    
    
    /**
     * @return Node[]
     */
    public function getResult()
    {
        return $this->result;
    }
}
