<?php
declare(strict_types=1);

namespace Haystack\Functional;

use Haystack\HaystackInterface;

class Filter
{
    /** @var array */
    private $arr;

    public function __construct(HaystackInterface $array)
    {
        $this->arr = $array->toArray();
    }

    public function filter(callable $func = null, string $flag = null): array
    {
        // Default
        if ($func === null) {
            $filtered = new FilterWithDefaults($this->arr);
            return $filtered->filter();
        }

        // No flags are passed
        if ($flag === null) {
            $filtered = new FilterWithValue($this->arr);
            return $filtered->filter($func);
        }

        // Flag of "USE_KEY" is passed
        if ('key' === $flag) {
            $filtered = new FilterWithKey($this->arr);
            return $filtered->filter($func);
        }

        // Flag of "BOTH" is passed
        if ('both' === $flag) {
            $filtered = new FilterWithValueAndKey($this->arr);
            return $filtered->filter($func);
        }

        throw new \InvalidArgumentException('Invalid flag name');
    }
}
