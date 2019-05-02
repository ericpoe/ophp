<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HArray;
use PHPUnit\Framework\TestCase;

class HArraySliceTest extends TestCase
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
     * @dataProvider firstPartOfArraySliceProvider
     */
    public function testGetFirstPartOfTypesOfArrayUsingSlice(string $type, HArray $expected): void
    {
        if ('list' === $type) {
            $subArray = $this->arrList->slice(0, 2);
        } else {
            $subArray = $this->arrDict->slice(0, 2);
        }

        $this->assertEquals($expected, $subArray);
    }

    public function firstPartOfArraySliceProvider(): array
    {
        return [
            'First two items of list' => ['list', new HArray(['apple', 'bobble'])],
            'First two items of dictionary' => ['dict', new HArray(['a' => 'apple', 'b' => 'bobble'])],
        ];
    }

    /**
     * @dataProvider lastPartOfArraySliceProvider
     */
    public function testGetLastPartOfTypesOfArrayUsingSlice(string $type, HArray $expected): void
    {
        if ('list' === $type) {
            $subArray = $this->arrList->slice(-2);
        } else {
            $subArray = $this->arrDict->slice(-2);
        }

        $this->assertEquals($expected, $subArray);
    }

    public function lastPartOfArraySliceProvider(): array
    {
        return [
            'Last two items of list' => ['list', new HArray(['cobble', 'dobble'])],
            'Last two items of dictionary' => ['dict', new HArray(['c' => 'cobble', 'd' => 'dobble'])],
        ];
    }

    /**
     * @dataProvider middlePartOfArraySliceProvider
     */
    public function testGetMiddlePartOfTypesOfArrayUsingSlice(string $type, ?int $start, ?int $length, HArray $expected): void
    {
        $start = $start ?? 0;

        if ('list' === $type) {
            $subArray = $this->arrList->slice($start, $length);
        } else {
            $subArray = $this->arrDict->slice($start, $length);
        }

        $this->assertEquals($expected, $subArray);
    }

    public function middlePartOfArraySliceProvider(): array
    {
        return [
            'List: Start -3, length: -1' => ['list', '-3', '-1', new HArray(['bobble', 'cobble'])],
            'List: Start 1, length: -1' => ['list', '1', '-1', new HArray(['bobble', 'cobble'])],
            'List: Start 1, length: 2' => ['list', '1', '2', new HArray(['bobble', 'cobble'])],
            'List: Start 1, length: null' => ['list', '1', null, new HArray(['bobble', 'cobble', 'dobble'])],
            'Dictionary: Start -3, length: -1' => ['dict', '-3', '-1', new HArray(['b' => 'bobble', 'c' => 'cobble'])],
            'Dictionary: Start 1, length: -1' => ['dict', '1', '-1', new HArray(['b' => 'bobble', 'c' => 'cobble'])],
            'Dictionary: Start 1, length: 2' => ['dict', '1', '2', new HArray(['b' => 'bobble', 'c' => 'cobble'])],
            'Dictionary: Start 1, length: null' => ['dict', '1', null, new HArray(['b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble'])],
        ];
    }
}
