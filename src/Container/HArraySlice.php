<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\HArray;

class HArraySlice
{
    /** @var array */
    private $arr;

    /**
     * @param HArray $array
     */
    public function __construct(HArray $array)
    {
        $this->arr = $array->toArray();
    }

    public function slice(int $start, ?int $length = null): array
    {
        $maintainIndices = false;

        return array_slice($this->arr, $start, $length, $maintainIndices);
    }

}
