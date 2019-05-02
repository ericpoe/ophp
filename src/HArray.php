<?php
declare(strict_types=1);

namespace Haystack;

use Haystack\Container\HArrayAppend;
use Haystack\Container\HArrayContains;
use Haystack\Container\HArrayInsert;
use Haystack\Container\HArrayLocate;
use Haystack\Container\HArrayRemove;
use Haystack\Container\HArraySlice;
use Haystack\Converter\ArrayToString;
use Haystack\Functional\Filter;
use Haystack\Functional\HArrayWalk;
use Haystack\Functional\HaystackMap;
use Haystack\Functional\HaystackReduce;

class HArray extends \ArrayObject implements HaystackInterface
{
    public const USE_KEY = 'key';
    public const USE_BOTH = 'both';

    /** @var array */
    protected $arr;

    public function __construct(?iterable $arr = [])
    {
        if ($arr instanceof \ArrayObject) {
            $arr = $arr->getArrayCopy();
        }

        if ($arr instanceof HString) {
            $arr = $arr->toArray();
        }

        parent::__construct((array) $arr);
        $this->arr = (array) $arr;
    }

    public function __toString(): string
    {
        $str = new ArrayToString($this->arr);

        return $str->toString();
    }

    public function toString(?string $glue = ''): string
    {
        $str = new ArrayToString($this->arr, (string) $glue);

        return $str->toString();

    }


    public function toArray(): array
    {
        return $this->arr;
    }

    /**
     * Alias to PHP function `implode`
     */
    public function toHString(?string $glue = ''): HString
    {
        $str = new ArrayToString($this->arr, (string) $glue);

        return new HString($str->toString());
    }

    /**
     * @inheritdoc
     */
    public function contains($value): bool
    {
        $answer = new HArrayContains($this);
        return $answer->contains($value);
    }

    /**
     * @inheritdoc
     */
    public function locate($value)
    {
        $answer = new HArrayLocate($this);

        if ($value instanceof HString) {
            $value = (string) $value;
        }

        return $answer->locate($value);
    }

    /**
     * @inheritdoc
     */
    public function append($value): HaystackInterface
    {
        $answer = new HArrayAppend($this->toArray());
        return new static($answer->append($value));
    }

    /**
     * @inheritdoc
     */
    public function insert($value, $key = null): HaystackInterface
    {
        $answer = new HArrayInsert($this);
        return new static($answer->insert($value, $key));
    }


    /**
     * @inheritdoc
     */
    public function remove($value): HaystackInterface
    {
        $answer = new HArrayRemove($this);
        return new static($answer->remove($value));
    }

    /**
     * @inheritdoc
     */
    public function slice(int $start, ?int $length = null): HaystackInterface
    {
        $answer = new HArraySlice($this);
        return new static($answer->slice($start, $length));
    }

    /**
     * @inheritdoc
     */
    public function map(callable $func): HaystackInterface
    {
        $containers = array_slice(func_get_args(), 1); // remove `$func`

        if (empty($containers)) {
            return new static((new HaystackMap($this))->map($func));
        }

        return new static((new HaystackMap($this))->map($func, $containers));
    }

    /**
     * @inheritdoc
     */
    public function walk(callable $func): void
    {
        HArrayWalk::walk($this->arr, $func);
    }

    /**
     * @inheritdoc
     */
    public function filter(callable $func = null, ?string $flag = null): HaystackInterface
    {
        $answer = new Filter($this);
        return new static($answer->filter($func, $flag));
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $func, $initial = null)
    {
        $answer = new HaystackReduce($this->arr);
        return $answer->reduce($func, $initial);
    }

    /**
     * @inheritdoc
     */
    public function head(): HaystackInterface
    {
        return $this->slice(0, 1);
    }

    /**
     * @inheritdoc
     */
    public function tail(): HaystackInterface
    {
        return $this->slice(1);
    }

    /**
     * @inheritdoc
     */
    public function sum(): float
    {
        return array_sum($this->arr);
    }

    /**
     * @inheritdoc
     */
    public function product(): float
    {
        if (empty($this->arr)) {
            return 0;
        }

        return array_product($this->arr);
    }
}
