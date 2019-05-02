<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\Helpers\Helper;
use Haystack\HString;

class HStringAppend
{
    /** @var string */
    private $aString;

    public function __construct(HString $hString)
    {
        $this->aString = (string) $hString;
    }

    public function append($value): string
    {
        if (is_scalar($value) || $value instanceof HString) {
            return sprintf('%s%s', $this->aString, (string) $value);
        }
        throw new \InvalidArgumentException(sprintf('Cannot concatenate an HString with a %s', Helper::getType($value)));
    }
}
