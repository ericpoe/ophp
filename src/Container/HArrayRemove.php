<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\HArray;

class HArrayRemove
{
    /** @var HArray */
    private $arr;

    public function __construct(HArray $arr)
    {
        $this->arr = $arr;
    }

    public function remove($value): array
    {
        if (false === $this->arr->contains($value)) {
            return $this->arr->toArray();
        }

        $newArr = $this->arr->toArray();
        $key = $this->arr->locate($value);
        unset($newArr[$key]);

        if ($this->allKeysNumeric(array_keys($newArr))) {
            return array_values($newArr);
        }

        return $newArr;
    }

    private function allKeysNumeric(array $keys): bool
    {
        return count($keys) === count(array_filter($keys, 'is_numeric'));
    }
}
