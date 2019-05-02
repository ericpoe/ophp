<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringMapTest extends TestCase
{
    /** @var HString */
    protected $aString;

    protected function setUp(): void
    {
        $this->aString = new HString('foobar');
    }

    public function testStringMap(): void
    {
        $capitalize = function ($letter) {
            return mb_strtoupper($letter);
        };

        $newString = $this->aString->map($capitalize);
        $this->assertEquals('FOOBAR', $newString);
    }

    public function testStringMapRot13(): void
    {
        $rot13 = function ($letter) {
            if (' ' === $letter || '-' === $letter) {
                return $letter;
            }

            return chr(97 + (ord($letter) - 97 + 13) % 26);
        };

        $newString = $this->aString->map($rot13);
        $expected = 'sbbone';
        $this->assertEquals($expected, $newString);
    }

    /**
     * @dataProvider arraysProvider
     */
    public function testStringWithOneArray(HString $expected, array $items): void
    {
        $action = function ($letterFromString, $wordFromArray) {
            return sprintf('-%s:%s-', $letterFromString, $wordFromArray);
        };

        $actual = (new HString('foo'))->map($action, $items);
        $this->assertEquals($expected, $actual);
    }

    public function arraysProvider(): array
    {
        $arrFewer = ['apple', 'butter'];
        $arrEqual = ['apple', 'butter', 'cookie'];
        $arrGreater = ['apple', 'butter', 'cookie', 'donut'];

        return [
            'One fewer' => [
                new HString('-f:apple--o:butter--o:-'),
                $arrFewer,
            ],
            'One equal' => [
                new HString('-f:apple--o:butter--o:cookie-'),
                $arrEqual,
            ],
            'One greater' => [
                new HString('-f:apple--o:butter--o:cookie--:donut-'),
                $arrGreater,
            ],
        ];
    }

    /**
     * @dataProvider HArraysProvider
     */
    public function testStringWithOneHArray(HString $expected, HArray $items): void
    {
        $action = function ($letterFromString, $wordFromArray) {
            return sprintf('-%s:%s-', $letterFromString, $wordFromArray);
        };

        $actual = (new HString('foo'))->map($action, $items);
        $this->assertEquals($expected, $actual);
    }

    public function HArraysProvider(): array
    {
        $arrFewer = new HArray(['apple', 'butter']);
        $arrEqual = new HArray(['apple', 'butter', 'cookie']);
        $arrGreater = new HArray(['apple', 'butter', 'cookie', 'donut']);

        return [
            'One fewer' => [
                new HString('-f:apple--o:butter--o:-'),
                $arrFewer,
            ],
            'One equal' => [
                new HString('-f:apple--o:butter--o:cookie-'),
                $arrEqual,
            ],
            'One greater' => [
                new HString('-f:apple--o:butter--o:cookie--:donut-'),
                $arrGreater,
            ],
        ];
    }

    /**
     * @dataProvider stringsProvider
     */
    public function testStringWithOneString(HString $expected, string $items): void
    {
        $action = function ($letterFromHaystack, $letterFromString) {
            return sprintf('-%s:%s-', $letterFromHaystack, $letterFromString);
        };

        $actual = $this->aString->map($action, $items);
        $this->assertEquals($expected, $actual);
    }

    public function stringsProvider(): array
    {
        $strFewer = 'apple';
        $strEqual = 'butter';
        $strGreater = 'cookies';

        return [
            'One fewer' => [
                new HString('-f:a--o:p--o:p--b:l--a:e--r:-'),
                $strFewer,
            ],
            'One equal' => [
                new HString('-f:b--o:u--o:t--b:t--a:e--r:r-'),
                $strEqual,
            ],
            'One greater' => [
                new HString('-f:c--o:o--o:o--b:k--a:i--r:e--:s-'),
                $strGreater,
            ],
        ];
    }

    /**
     * @dataProvider HStringsProvider
     */
    public function testStringWithOneHString(HString $expected, HString $items): void
    {
        $action = function ($letterFromHaystack, $letterFromString) {
            return sprintf('-%s:%s-', $letterFromHaystack, $letterFromString);
        };

        $actual = $this->aString->map($action, $items);
        $this->assertEquals($expected, $actual);
    }

    public function HStringsProvider(): array
    {
        $strFewer = new HString('apple');
        $strEqual = new HString('butter');
        $strGreater = new HString('cookies');

        return [
            'One fewer' => [
                new HString('-f:a--o:p--o:p--b:l--a:e--r:-'),
                $strFewer,
            ],
            'One equal' => [
                new HString('-f:b--o:u--o:t--b:t--a:e--r:r-'),
                $strEqual,
            ],
            'One greater' => [
                new HString('-f:c--o:o--o:o--b:k--a:i--r:e--:s-'),
                $strGreater,
            ],
        ];
    }

    public function testErrorGetsThrown(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('stdClass cannot be mapped');

        $action = function ($letterFromHaystack, $thingFromBadThings) {
            return sprintf('%s:%s', $letterFromHaystack, $thingFromBadThings);
        };

        $badStr = new \stdClass();

        $this->aString->map($action, $badStr);
    }
}
