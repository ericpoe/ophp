<?php
declare(strict_types=1);

namespace Haystack\Helpers;

use Haystack\HString;

class Helper
{
    public static function getType($thing): string
    {
        if (is_object($thing)) {
            return get_class($thing);
        }

        return gettype($thing);
    }

    /**
     * Determines if an array is associative or not
     *
     * @link http://stackoverflow.com/questions/173400
     */
    public static function isAssociativeArray(array $array): bool
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    public static function canBeInArray($thing): bool
    {
        $possibility = is_array($thing)
            || is_scalar($thing)
            || $thing instanceof \ArrayObject
            || $thing instanceof HString;

        return $possibility;
    }
}
