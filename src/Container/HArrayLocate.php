<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\HArray;

class HArrayLocate
{
    /** @var HArray */
    private $arr;

    public function __construct(HArray $arr)
    {
        $this->arr = $arr;
    }

    /**
     * @param mixed $value
     * @return int|string
     * @throws ElementNotFoundException
     */
    public function locate($value)
    {
        $foundItem = array_search($value, $this->arr->toArray(), true);

        if (false !== $foundItem) {
            return $foundItem;
        }

        throw new ElementNotFoundException($value);
    }
}
