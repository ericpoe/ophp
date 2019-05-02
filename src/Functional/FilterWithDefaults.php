<?php
declare(strict_types=1);

namespace Haystack\Functional;

use Haystack\Helpers\Helper;

class FilterWithDefaults
{
    /** @var array*/
    protected $arr;

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public function filter(): array
    {
        $filtered = array_filter($this->arr);

        if (Helper::isAssociativeArray($filtered)) {
            return $filtered;
        }

        return array_values($filtered);
    }
}
