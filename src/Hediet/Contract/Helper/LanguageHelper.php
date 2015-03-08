<?php

namespace Hediet\Contract\Helper;

class LanguageHelper
{
    public static function joinWithAnd(array $parts)
    {
        $i = count($parts);
        $result = "";
        foreach ($parts as $p)
        {
            $i--;
            if ($result === "")
                $result = $p;
            else if ($i === 0)
                $result = $result . " and " . $p;
            else
                $result = $result . ", " .$p;
        }
        
        return $result;
    }
    
    public static function joinSentences(array $sentences)
    {
        $items = array();
        $isFirst = true;
        foreach ($sentences as $sentence)
        {
            if ($isFirst)
                $isFirst = false;
            else
                $sentence = lcfirst($sentence);
            
            if (substr($sentence, -1, 1) === ".")
                $sentence = substr($sentence, 0, -1);
            
            $items[] = $sentence;
        }
        
        return self::joinWithAnd($items) . ".";
    }
}
