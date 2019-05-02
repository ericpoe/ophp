<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HArrayMapTest extends TestCase
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

    public function testArrayMapWithNoExtra(): void
    {
        $capitalizeList = function ($word) {
            return strtoupper($word);
        };

        $newArrList = $this->arrList->map($capitalizeList);
        $this->assertEquals('APPLE', $newArrList[0]);

        $newArrDict = $this->arrDict->map($capitalizeList);
        $this->assertEquals('APPLE', $newArrDict['a']);
    }

    /**
     * @dataProvider oneExtraArraysProvider
     *
     * @param HArray   $expected
     * @param HArray   $numArray - variadic array
     */
    public function testArrayMapWithOneExtra(HArray $numArray, HArray $expected): void
    {
        $action = function ($word, $numWord) {
            return sprintf('I have %s %s', $numWord, $word);
        };

        $actual = $this->arrList->map($action, $numArray);
        $this->assertEquals($expected, $actual);
    }

    public function oneExtraArraysProvider(): array
    {
        $numFewer = new HArray(['one', 'two', 'three']);
        $numEqual = new HArray(['one', 'two', 'three', 'four']);
        $numGreater = new HArray(['one', 'two', 'three', 'four', 'five']);

        return [
            'One-Extra array size of one fewer does meet expectations' => [
                $numFewer,
                new HArray([
                    'I have one apple',
                    'I have two bobble',
                    'I have three cobble',
                    'I have  dobble' // extra space due to missing placeholder
                ]),
            ],
            'One-Extra array size of equivalence does not meet expectations' => [
                $numEqual,
                new HArray([
                    'I have one apple',
                    'I have two bobble',
                    'I have three cobble',
                    'I have four dobble'
                ]),
            ],
            'One-Extra array size of one greater does not meet expectations' => [
                $numGreater,
                new HArray([
                    'I have one apple',
                    'I have two bobble',
                    'I have three cobble',
                    'I have four dobble',
                    'I have five ' // extra space due to missing placeholder
                ]),
            ],
        ];
    }

    /**
     * @dataProvider twoExtraArraysProvider
     *
     * @param HArray   $expected
     * @param HArray   $numArray - variadic array
     * @param HArray   $adjArray - variadic array
     */
    public function testArrayMapWithTwoExtras(HArray $numArray, HArray $adjArray, HArray $expected): void
    {
        $action = function ($noun, $numWord, $adjective) {
            return sprintf('I have %s %s %s', $numWord, $adjective, $noun);
        };

        $actual = $this->arrList->map($action, $numArray, $adjArray);
        $this->assertEquals($expected, $actual);
    }

    public function twoExtraArraysProvider(): array
    {
        $numFewer = new HArray(['one', 'two', 'three']);
        $numEqual = new HArray(['one', 'two', 'three', 'four']);
        $numGreater = new HArray(['one', 'two', 'three', 'four', 'five']);

        $adjFewer = new HArray(['large', 'small']);
        $adjEqual = new HArray(['large', 'small', 'ripe', 'rotten']);
        $adjGreater = new HArray(['large', 'small', 'ripe', 'rotten', 'red', 'green']);

        return [
            'Two-Extra array size of fewer does not meet expectations' => [
                $numFewer,
                $adjFewer,
                new HArray([
                    'I have one large apple',
                    'I have two small bobble',
                    'I have three  cobble', // extra spaces due to missing placeholder
                    'I have   dobble' // extra spaces due to missing placeholders
                ]),
            ],
            'Two-Extra array size of equivalence does not meet expectations' => [
                $numEqual,
                $adjEqual,
                new HArray([
                    'I have one large apple',
                    'I have two small bobble',
                    'I have three ripe cobble',
                    'I have four rotten dobble'
                ]),
            ],
            'Two-Extra array size of greater does not meet expectations' => [
                $numGreater,
                $adjGreater,
                new HArray([
                    'I have one large apple',
                    'I have two small bobble',
                    'I have three ripe cobble',
                    'I have four rotten dobble',
                    'I have five red ', // extra spaces due to missing placeholder
                    'I have  green ' // extra spaces due to missing placeholders
                ]),
            ],
        ];
    }

    public function testArrayMapWithVariadicHString(): void
    {
        $phrase = new HString('abcd');

        $sentence = function ($word, $letter) {
            return sprintf('%s starts with %s', $word, $letter);
        };

        $oddThing = $this->arrList->map($sentence, $phrase);

        $expected = new HArray([
            'apple starts with a',
            'bobble starts with b',
            'cobble starts with c',
            'dobble starts with d',
        ]);
        $this->assertEquals($expected, $oddThing);
    }

    public function testErrorGetsThrown(): void
    {
        $badStr = new \stdClass();


        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('stdClass cannot be mapped');

        $action = function ($letterFromHaystack, $thingFromBadThings) {
            return sprintf('%s:%s', $letterFromHaystack, $thingFromBadThings);
        };

        $this->arrList->map($action, $badStr);
    }
}
