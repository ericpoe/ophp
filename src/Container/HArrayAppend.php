<?php
namespace Haystack\Container;

use Haystack\HArray;

class HArrayAppend
{
    /** @var \ArrayObject */
    private $arr;

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->arr = new \ArrayObject($array);
    }

    /**
     * @param HArray|array|int|float|string $value
     * @return array
     */
    public function append($value)
    {
        $value = $value instanceof HArray ? $value->toArray() : $value;

        $this->arr->append($value);

        return $this->arr->getArrayCopy();
    }

}
