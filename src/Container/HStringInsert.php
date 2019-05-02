<?php
namespace Haystack\Container;

use Haystack\HString;

class HStringInsert
{
    /** @var HString */
    private $hString;

    public function __construct(HString $hString)
    {
        $this->hString = $hString;
    }

    public function insert(string $value, ?int $key = null): string
    {
        if ($key === null) {
            return $this->buildString($value, $this->hString->count());
        }

        return $this->buildString($value, $key);
    }

    private function buildString(string $value, int $key): string
    {
        return sprintf('%s%s%s', $this->getPrefix($key), $value, $this->getSuffix($key));
    }

    private function getPrefix(int $key): string
    {
        $length = $key >= 0 ? $key: $this->hString->count() - 1;

        return mb_substr($this->hString, 0, $length, $this->hString->getEncoding());
    }

    private function getSuffix(int $key): string
    {
        $start = $key >= 0 ? $key : $this->hString->count() + $key;
        $length = $key >= 0 ? $this->hString->count() : $this->hString->count() + $key;

        return mb_substr($this->hString, $start, $length, $this->hString->getEncoding());
    }
}
