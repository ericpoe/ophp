<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HArrayReduceTest extends TestCase
{
    /** @var HArray */
    private $arrList;

    /** @var HArray */
    private $arrDict;

    protected function setUp(): void
    {
        $this->arrList = new HArray(['apple', 'bobble', 'cobble', 'dobble']);
        $this->arrDict = new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble']);
    }

    /**
     * @dataProvider arrayReduceProvider
     */
    public function testArrayReduce(HArray $testArr, int $expected): void
    {
        $sum = function ($carry, $item) {
            $carry += (int) $item;
            return $carry;
        };

        $this->assertEquals($expected, $testArr->reduce($sum));
    }

    public function arrayReduceProvider(): array
    {
        return [
            'Empty Array' => [new HArray(), 0],
            'List: Array of Strings' => [new HArray($this->arrList), 0],
            'List: Array of Strings & Int' => [new HArray(['apple', 'bobble', 'cobble', 5]), 5],
            'List: Array of Int' => [new HArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]), 55],
            'List: Array of Int & Int Strings' => [new HArray(['1', 2, '3', 4, '5', 6, '7', 8, '9', 10]), 55],
            'Dictionary: Array of Strings' => [new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble']), 0],
            'Dictionary: Array of Strings & Int' => [new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => '5']), 5],
            'Dictionary: Array of Int' => [new HArray(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10]), 55],
            'Dictionary: Array of Int & Int Strings' => [new HArray(['a' => 1, 'b' => '2', 'c' => 3, 'd' => '4', 'e' => 5, 'f' => '6', 'g' => 7, 'h' => '8', 'i' => 9, 'j' => '10']), 55],
        ];
    }

    /**
     * @dataProvider arrayReduceWithInitProvider
     */
    public function testArrayReduceWithInit(HArray $testArr, int $init, int $expected): void
    {
        $sum = function ($carry, $item) {
            $carry += $item;
            return $carry;
        };

        $this->assertEquals($expected, $testArr->reduce($sum, $init));
    }

    public function arrayReduceWithInitProvider(): array
    {
        $fullArr = new HArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        return [
            'Full array with negative init' => [$fullArr, -10, 45],
            'Full array with positive init' => [$fullArr, 10, 65],
            'Empty array with negative init' => [new HArray(), -10, -10],
            'Empty array with positive init' => [new HArray(), 10, 10],
        ];
    }

    /**
     * @dataProvider reduceAsArrayTypeProvider
     */
    public function testReduceAsArrayType(callable $freq): void
    {
        $this->assertInstanceOf(HArray::class, $this->arrList->reduce($freq));
        $this->assertInstanceOf(HArray::class, $this->arrDict->reduce($freq));
    }

    public function reduceAsArrayTypeProvider(): array
    {
        $freqArray = function ($frequency, $letter) {
            if (!isset($frequency[$letter])) {
                $frequency[$letter] = 0;
            }

            $frequency[$letter]++;

            return $frequency;
        };

        $freqArrayObject = function ($frequency, $letter) {
            if (!isset($frequency[$letter])) {
                $frequency[$letter] = 0;
            }

            $frequency = new \ArrayObject($frequency);

            $frequency[$letter]++;

            return $frequency;
        };

        $freqHArray = function ($frequency, $letter) {
            if (!isset($frequency[$letter])) {
                $frequency[$letter] = 0;
            }

            $frequency = new HArray($frequency);

            $frequency[$letter]++;

            return $frequency;
        };

        return [
            'Array' => [$freqArray],
            'ArrayObject' => [$freqArrayObject],
            'HArray' => [$freqHArray],
        ];
    }

    public function testReduceAsString(): void
    {
        $toString = function ($sentence, $word) {
            return sprintf('%s%s ', $sentence, $word);
        };

        $this->assertEquals(new HString('apple bobble cobble dobble'), trim((string) $this->arrList->reduce($toString)));
        $this->assertEquals(new HString('apple bobble cobble dobble'), trim((string) $this->arrDict->reduce($toString)));
        $this->assertInstanceOf(HString::class, $this->arrList->reduce($toString));
        $this->assertInstanceOf(HString::class, $this->arrDict->reduce($toString));
    }
}
