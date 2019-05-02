<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\HString;

class HStringRemove
{
    /** @var HString */
    private $hString;

    public function __construct(HString $hString)
    {
        $this->hString = $hString;
    }

    public function remove(string $value): string
    {
        $key = $this->hString->locate($value);

        return sprintf('%s%s', $this->getPrefix($key), $this->getSuffix($key));
    }

    private function getPrefix(int $length): string
    {
        return mb_substr((string) $this->hString, 0, $length, $this->hString->getEncoding());
    }

    private function getSuffix(int $start): string
    {
        return mb_substr((string) $this->hString, $start + 1, $this->hString->count() - $start, $this->hString->getEncoding());
    }
}
