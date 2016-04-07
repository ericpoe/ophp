<?php
namespace Haystack\Functional;

use Haystack\HString;

class HStringFilter
{
    /** @var HString */
    protected $hString;

    public function __construct(HString $hString)
    {
        $this->hString = $hString;
    }
    public function filter(callable $func = null, $flag = null)
    {
        // Default
        if (is_null($func)) {
            $filtered = new HStringFilterWithDefaults($this->hString);
            return $filtered->filter();
        }

        // No flags are passed
        if (is_null($flag)) {
            $filtered = new HStringFilterWithValue($this->hString);
            return $filtered->filter($func);
        }

        if ("key" === $flag || "both" === $flag) {
            // Flag of "USE_KEY" is passed
            if ("key" === $flag) {
                $filtered = new HStringFilterWithKey($this->hString);
                return $filtered->filter($func);
            }

            // Flag of "USE_BOTH is passed
            $filtered = new HStringFilterWithValueAndKey($this->hString);
            return $filtered->filter($func);
        } else {
            throw new \InvalidArgumentException("Invalid flag name");
        }
    }
}
