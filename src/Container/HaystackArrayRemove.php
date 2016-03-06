<?php
namespace Haystack\Container;

use Haystack\Helpers\Helper;
use Haystack\HArray;

class HaystackArrayRemove
{
    private $arr;

    public function __construct(HArray $array)
    {
        $this->arr = $array;
    }

    public function remove($value)
    {
        if (Helper::canBeInArray($value)) {
            if (false === $this->arr->contains($value)) {
                return $this->arr;
            }

            $newArr = $this->arr->toArray();
            $key = $this->arr->locate($value);
        } else {
            throw new \InvalidArgumentException(sprintf("%s cannot be contained within an HArray", Helper::getType($value)));
        }

        if (is_numeric($key)) {
            unset($newArr[$key]);

            return array_values($newArr);
        }

        // key is string
        unset($newArr[$key]);

        return $newArr;
    }
}
