<?php
declare(strict_types=1);

namespace Haystack\Functional;

class HArrayWalk
{
    public static function walk(array $arr, callable $func): void
    {
        array_walk($arr, $func);
    }
}
