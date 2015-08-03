<?php
namespace OPHP\Filter;

use OPHP\OString;
use OPHP\OStringFilter;

class FilterWithValueAndKey extends OStringFilter
{
    /**
     * @param OString  $string
     * @param OString  $filtered
     * @param callable $func
     */
    public function __construct(OString &$string, OString &$filtered, callable &$func)
    {
        foreach ($string as $letter) {
            if (true === (bool) $func($letter, $string->key())) {
                $filtered = $filtered->insert($letter);
            }
        }
    }
}
