<?php
declare(strict_types=1);

namespace Haystack;

use Haystack\Container\ElementNotFoundException;
use Haystack\Container\HStringAppend;
use Haystack\Container\HStringContains;
use Haystack\Container\HStringInsert;
use Haystack\Container\HStringLocate;
use Haystack\Container\HStringRemove;
use Haystack\Container\HStringSlice;
use Haystack\Converter\StringToArray;
use Haystack\Functional\Filter;
use Haystack\Functional\HaystackMap;
use Haystack\Functional\HaystackReduce;
use Haystack\Functional\HStringWalk;
use Haystack\Helpers\Helper;

class HString implements HaystackInterface
{
    public const USE_KEY = 'key';
    public const USE_BOTH = 'both';

    /** @var string */
    protected $str;

    /**
     * @var int
     *
     * Pointer for iterating through $str
     */
    protected $ptr;

    /**
     * @var string
     *
     * Defaults to UTF-8 encoding
     */
    protected $encoding;

    public function __construct(?string $str = '')
    {
        $this->encoding = 'UTF-8';

        $stringy = mb_convert_encoding((string) $str, $this->encoding);

        $this->str = $stringy;
        $this->rewind();
    }

    public function __toString(): string
    {
        return (string) $this->str;
    }

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether an offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset): bool
    {
        return $offset >= 0 && $offset < $this->count();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return string
     */
    public function offsetGet($offset): string
    {
        return mb_substr($this->str, $offset, 1, $this->encoding);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->str = $this->getPrefix($offset) . $value . $this->getSuffix($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->str = $this->getPrefix($offset) . chr(0x00) . $this->getSuffix($offset);
    }

    private function getPrefix($length): string
    {
        return mb_substr((string) $this, 0, $length, $this->getEncoding());
    }

    private function getSuffix($start): string
    {
        return mb_substr((string) $this, $start + 1, $this->count() - $start, $this->getEncoding());
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize(): ?string
    {
        return serialize($this->__toString());
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param null|string $value <p>
     *                           The string representation of the object.
     *                           </p>
     * @return void
     */
    public function unserialize($value): void
    {
        if (is_string($value)) {
            $this->str = unserialize($value, [$this]);
        } else {
            throw new \InvalidArgumentException(sprintf('HString cannot unserialize a %s', Helper::getType($value)));
        }
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count(): int
    {
        return mb_strlen($this->str);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return string
     */
    public function current(): string
    {
        return mb_substr($this->str, $this->ptr, 1, $this->encoding);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next(): void
    {
        ++$this->ptr;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return integer scalar on success, or null on failure.
     */
    public function key(): ?int
    {
        return $this->ptr;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid(): bool
    {
        return $this->ptr < $this->count();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind(): void
    {
        $this->ptr = 0;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function toString(string $glue = ''): string
    {
        if ($glue === '') {
            return (string) $this;
        }

        return $this
            ->toHArray()
            ->toString($glue);
    }

    /**
     * Converts a string into an array. Assumes a delimiter of "" to return an array of chars.
     */
    public function toArray(): array
    {
        if (empty($this->str)) {
            return [];
        }

        return (new StringToArray($this->str))
            ->stringToArray();
    }

    /**
     * Alias to PHP function `explode`
     */
    public function toHArray(?string $delim = '', ?int $limit = 0): HArray
    {
        if (empty($this->str)) {
            return new HArray();
        }

        $arr = new StringToArray($this->str, $delim ?? '');

        return new HArray($arr->stringToArray($limit));
    }

    /**
     * @inheritdoc
     *
     * @param HString|string $value
     * @return bool
     */
    public function contains($value): bool
    {
        $answer = new HStringContains($this);
        return $answer->contains($value);
    }

    /**
     * @inheritdoc
     *
     * @param HString|string $value
     * @return int key of $value in current object
     * @throws ElementNotFoundException
     */
    public function locate($value): int
    {
        $answer = new HStringLocate($this);
        return $answer->locate($value);
    }

    /**
     * @inheritdoc
     *
     * @param string $value
     * @return HaystackInterface
     */
    public function append($value): HaystackInterface
    {
        $answer = new HStringAppend($this);
        return new static($answer->append($value));
    }

    /**
     * @inheritdoc
     *
     * @param string $value
     * @param int|null $key
     * @return HaystackInterface
     */
    public function insert($value, $key = null): HaystackInterface
    {
        $answer = new HStringInsert($this);
        return new static($answer->insert($value, $key));
    }

    /**
     * @inheritdoc
     *
     * @param string $value
     * @return HaystackInterface
     */
    public function remove($value): HaystackInterface
    {
        $answer = new HStringRemove($this);
        return new static($answer->remove($value));
    }

    /**
     * @inheritdoc
     */
    public function slice(int $start, ?int $length = null): HaystackInterface
    {
        $answer = new HStringSlice($this);
        return new static($answer->slice($start, $length));
    }

    /**
     * @inheritdoc
     */
    public function map(callable $func): HaystackInterface
    {
        $containers = array_slice(func_get_args(), 1); // remove `$func`
        $haystack = $this->toHArray();

        if (empty($containers)) {
            return new static((new HArray((new HaystackMap($haystack))->map($func)))->toHString()->toString());
        }

        return new static((new HArray((new HaystackMap($haystack))->map($func, $containers)))->toHString()->toString());
    }

    /**
     * @inheritdoc
     */
    public function walk(callable $func): void
    {
        HStringWalk::walk($this, $func);
    }

    /**
     * @inheritdoc
     */
    public function filter(?callable $func = null, ?string $flag = null): HaystackInterface
    {
        $answer = new Filter($this->toHArray());
        return new static((new HArray($answer->filter($func, $flag)))->toHString()->toString());
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $func, $initial = null)
    {
        $answer = new HaystackReduce($this->toArray());
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
        $values = new HArray(str_getcsv(str_ireplace(' ', '', $this->str)));

        return $values->sum();
    }

    /**
     * @inheritdoc
     */
    public function product(): float
    {
        $values = new HArray(str_getcsv(str_ireplace(' ', '', $this->str)));

        return $values->product();
    }
}
