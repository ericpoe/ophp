<?php
declare(strict_types=0);

namespace Haystack\Container;

use Haystack\HString;

class HStringLocate
{
    /** @var HString */
    private $hString;

    public function __construct(HString $str)
    {
        $this->hString = $str;
    }

    /**
     * @param HString|string $value
     * @return int
     * @throws ElementNotFoundException
     */
    public function locate($value): int
    {
        if ($this->hString->contains($value)) {
            $location = mb_strpos($this->hString, (string) $value, 0, $this->hString->getEncoding());

            if ($location !== false) {
                return $location;
            }
        }

        throw new ElementNotFoundException((string) $value);
    }

}
