<?php
declare(strict_types=1);

namespace Haystack\Container;

use Haystack\Helpers\Helper;
use Haystack\HString;

class HStringContains
{
    /** @var string */
    private $str;

    /** @var string */
    private $value;

    private $encoding;

    public function __construct(HString $hString)
    {
        $this->str = $hString->toString();
        $this->encoding = $hString->getEncoding();
    }

    /**
     * @param HString|string $value
     * @return bool
     */
    public function contains($value): bool
    {
        if (is_scalar($value) || method_exists($value, '__toString')) {
            $this->value = (string) $value;
        } else {
            throw new \InvalidArgumentException(sprintf('%s cannot be converted to a string; it cannot be used as a search value within an HString', Helper::getType($value)));
        }

        return $this->containsValue();
    }

    private function containsValue(): bool
    {
        return false !== mb_strpos($this->str, $this->value, 0, $this->encoding);
    }
}
