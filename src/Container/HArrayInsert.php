<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\HArray;
use Haystack\Helpers\Helper;
use Haystack\HString;

class HArrayInsert
{
    /** @var HArray */
    private $arr;

    public function __construct(HArray $array)
    {
        $this->arr = $array;
    }

    public function insert($value, $key): array
    {
        if ($value instanceof HArray) {
            $value = $value->toArray();
        }

        if ($value instanceof HString) {
            $value = $value->toString();
        }

        if (Helper::canBeInArray($value)) {
            $valueArray = $value;
        } else {
            $valueArray = [$value];
        }

        if (isset($key)) {
            if (is_numeric($key)) {
                list($array, $length) = $this->setSubarrayAndLengthForSequentialArray($key, $valueArray);
            } elseif (is_string($key)) {
                list($array, $length) = $this->setSubarrayAndLengthForAssociativeArray($key, $valueArray);
            } else {
                throw new \InvalidArgumentException('Invalid array key');
            }
        } else {
            list($array, $length) = $this->setSubarrayAndLengthWhenNoKeyProvided($valueArray);
        }

        $first = $this->arr->slice(0, $length)->toArray();
        $lastStartingPoint = count($this->arr) - count($first);
        $last = $this->arr->slice($length, $lastStartingPoint)->toArray();

        return array_merge_recursive($first, (array) $array, $last);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    private function setSubarrayAndLengthForSequentialArray($key, $value): array
    {
        $array = $value;
        $length = (int) $key;

        return [$array, $length];
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return array
     */
    private function setSubarrayAndLengthForAssociativeArray(string $key, $value): array
    {
        $array = [$key => $value];
        $length = count($this->arr);

        return [$array, $length];
    }

    /**
     * @param mixed $value
     * @return array
     */
    private function setSubarrayAndLengthWhenNoKeyProvided($value): array
    {
        $array = $value;
        $length = count($this->arr);

        return [$array, $length];
    }
}
