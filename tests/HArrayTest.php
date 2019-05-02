<?php
declare(strict_types=1);

namespace Haystack\Tests;

use Haystack\HArray;
use Haystack\HaystackInterface;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HArrayTest extends TestCase
{
    /** @var HArray */
    private $arrList;

    /** @var HArray */
    private $arrDict;

    /** @var HArray */
    private $arrUtf8Dict;

    protected function setUp(): void
    {
        $this->arrList = new HArray(['apple', 'bobble', 'cobble', 'dobble']);
        $this->arrDict = new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble']);
        $this->arrUtf8Dict = new HArray(['ɐ' => 'ǝlddɐ', 'q' => 'ǝlqqoq', 'ɔ' => 'ǝlqqoɔ', 'p' => 'ǝlqqop']);
    }

    public function testCreateEmptyArray(): void
    {
        $array = new HArray();
        $this->assertEmpty($array);

        $emptyArr = array();
        $array = new HArray($emptyArr);
        $this->assertEmpty($array);
    }

    /**
     * @dataProvider goodArraysProvider
     */
    public function testCreateArrayOfThings(iterable $item): void
    {
        $goodArr = new HArray($item);
        $this->assertArrayHasKey(0, $goodArr->toArray());
    }

    public function goodArraysProvider(): array
    {
        return [
            'array' => [[1, 2, 3]],
            'ArrayObject' => [new \ArrayObject([0, 1, 2])],
            'HString' => [new HString('a string')],
            'HString of HString of ... ' => [new HString((string) new HString((string) new HString((string) new HString('a string'))))],
        ];
    }

    public function testArrayStyleAccess(): void
    {
        $this->assertEquals('bobble', $this->arrList[1]);
        $this->assertEquals('bobble', $this->arrDict['b']);
        $this->assertEquals('ǝlqqoq', $this->arrUtf8Dict['q']);
    }

    public function testArrayHead(): void
    {
        $this->assertEquals(new HArray(['apple']), $this->arrList->head());
        $this->assertEquals(new HArray(['a' => 'apple']), $this->arrDict->head());
        $this->assertEquals(new HArray(['ɐ' => 'ǝlddɐ']), $this->arrUtf8Dict->head());
    }

    public function testArrayTail(): void
    {
        $this->assertEquals(new HArray(['bobble', 'cobble', 'dobble']), $this->arrList->tail());
        $this->assertEquals(new HArray(['b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble']), $this->arrDict->tail());
    }

    /**
     * @dataProvider arraySumProvider
     */
    public function testArraySum(HArray $testArr, int $expected): void
    {
        $this->assertEquals($expected, $testArr->sum());
    }

    public function arraySumProvider(): array
    {
        return [
            'Empty HArray' => [new HArray(), 0],
            'List: Array of Strings' => [new HArray($this->arrList), 0],
            'List: Array of Strings & Int' => [new HArray(['apple', 'bobble', 'cobble', 5]), 5],
            'Dictionary: Array of Strings' => [new HArray($this->arrDict), 0],
            'Dictionary: Array of Strings & Int' => [new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => '5']), 5],
            'List: Array of Ints' => [new HArray(range(1, 10)), 55],
            'List: Array of Ints and String Ints' => [new HArray(['1', 2, '3', 4, '5', 6, '7', 8, '9', 10]), 55],
            'Dictionary: Array of Ints' => [new HArray(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10]), 55],
        ];
    }

    /**
     * @dataProvider arrayProductProvider()
     */
    public function testArrayProduct(HaystackInterface $testArr, int $expected): void
    {
        $this->assertEquals($expected, $testArr->product());
    }

    public function arrayProductProvider(): array
    {
        return [
            'Empty HArray' => [new HArray(), 0],
            'List: Array of Strings' => [new HArray(['apple', 'bobble', 'cobble']), 0],
            'List: Array of Ints' => [new HArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]), 3628800],
            'List: Array of Strings & Int' => [new HArray(['apple', 'bobble', 'cobble', 5]), 0],
            'List: Array of String Ints & Int' => [new HArray(['1', 2, '3', 4, '5', 6, '7', 8, '9', 10]), 3628800],
            'Dictionary: Array of Strings' => [new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble']), 0],
            'Dictionary: Array of Strings & Int' => [new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => '5']), 0],
            'Dictionary: Array of Ints' => [new HArray(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10]), 3628800],
        ];
    }
}
