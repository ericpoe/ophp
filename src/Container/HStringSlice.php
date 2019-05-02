<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\HString;

class HStringSlice
{
    /** @var HString */
    private $str;

    /**
     * @param HString $hString
     */
    public function __construct(HString $hString)
    {
        $this->str = $hString;
    }

    public function slice(int $start, ?int $length = null): string
    {
        return mb_substr((string) $this->str, $start, $length, $this->str->getEncoding());
    }
}
