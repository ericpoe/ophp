<?php
declare(strict_types=1);

namespace Haystack\Converter;

class ArrayToString
{
    /** @var array */
    private $arr;

    /** @var string */
    private $glue;

    public function __construct(array $arr, string $glue = '')
    {
        $this->arr = $arr;
        $this->glue = $glue;
    }

    public function toString(): string
    {
        return implode($this->glue, $this->arr);
    }
}
