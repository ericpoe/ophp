<?php
declare(strict_types=1);

namespace Haystack\Functional;

class FilterWithValueAndKey
{
    /** @var array */
    protected $arr;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public function filter(callable $func): array
    {
        return array_filter($this->arr, $func, ARRAY_FILTER_USE_BOTH);
    }
}
