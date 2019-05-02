<?php
declare(strict_types=1);

namespace Haystack\Tests\Converter;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class ArrayToStringTest extends TestCase
{
    /**
     * @dataProvider arrayToHstringProvider
     */
    public function testArrayToHstring(HArray $arr, string $expected): void
    {
        $this->assertEquals($expected, $arr->toHString());
    }

    public function arrayToHstringProvider(): array
    {
        return [
            'Empty Array' => [new HArray(), new HString()],
            '1-item list' => [new HArray(['apple']), new HString('apple')],
            '1-item dict' => [new HArray(['a' => 'apple']), new HString('apple')],
            '2-item list' => [new HArray(['apple', 'banana']), new HString('applebanana')],
            '2-item dict' => [new HArray(['a' => 'apple', 'b' => 'banana']), new HString('applebanana')],
        ];
    }

    /**
     * @dataProvider arrayToStringProvider
     */
    public function testArrayToStringCast(HArray $arr, string $expected): void
    {
        $this->assertEquals($expected, (string) $arr);
    }

    public function arrayToStringProvider(): array
    {
        return [
            'Empty Array' => [new HArray(), ''],
            '1-item list' => [new HArray(['apple']), 'apple'],
            '1-item dict' => [new HArray(['a' => 'apple']), 'apple'],
            '2-item list' => [new HArray(['apple', 'banana']), 'applebanana'],
            '2-item dict' => [new HArray(['a' => 'apple', 'b' => 'banana']), 'applebanana'],
        ];
    }

    /**
     * @dataProvider arrayToStringWithGlueProvider()
     */
    public function testArrayToStringWithGlue(Harray $arr, ?string $glue, string $expected): void
    {
        $this->assertEquals($expected, $arr->toString($glue));
    }

    public function arrayToStringWithGlueProvider(): array
    {
        return [
            'Empty Array, null glue' => [new HArray(), null, ''],
            '1-item list, null glue' => [new HArray(['apple']), null, 'apple'],
            '1-item dict, null glue' => [new HArray(['a' => 'apple']), null, 'apple'],
            '2-item list, null glue' => [new HArray(['apple', 'banana']), null, 'applebanana'],
            '2-item dict, null glue' => [new HArray(['a' => 'apple', 'b' => 'banana']), null, 'applebanana'],
            'Empty Array, space glue' => [new HArray(), ' ', ''],
            '1-item list, space glue' => [new HArray(['apple']), ' ', 'apple'],
            '1-item dict, space glue' => [new HArray(['a' => 'apple']), ' ', 'apple'],
            '2-item list, space glue' => [new HArray(['apple', 'banana']), ' ', 'apple banana'],
            '2-item dict, space glue' => [new HArray(['a' => 'apple', 'b' => 'banana']), ' ', 'apple banana'],
        ];
    }

    /**
     * @dataProvider arrayToHstringWithGlueProvider
     */
    public function testArrayToHstringWithGlue(HArray $arr, ?string $glue, string $expected): void
    {
        $this->assertEquals($expected, $arr->toHString($glue));
    }

    public function arrayToHstringWithGlueProvider(): array
    {
        return [
            'Empty Array, null glue' => [new HArray(), null, new HString()],
            '1-item list, null glue' => [new HArray(['apple']), null, new HString('apple')],
            '1-item dict, null glue' => [new HArray(['a' => 'apple']), null, new HString('apple')],
            '2-item list, null glue' => [new HArray(['apple', 'banana']), null, new HString('applebanana')],
            '2-item dict, null glue' => [new HArray(['a' => 'apple', 'b' => 'banana']), null, new HString('applebanana')],
            '1-item list, space glue' => [new HArray(['apple']), ' ', new HString('apple')],
            '1-item dict, space glue' => [new HArray(['a' => 'apple']), ' ', new HString('apple')],
            '2-item list, space glue' => [new HArray(['apple', 'banana']), ' ', new HString('apple banana')],
            '2-item dict, space glue' => [new HArray(['a' => 'apple', 'b' => 'banana']), ' ', new HString('apple banana')],
            '1-item list, HString glue' => [new HArray(['apple']), new HString(' '), new HString('apple')],
            '1-item dict, HString glue' => [new HArray(['a' => 'apple']), new HString(' '), new HString('apple')],
            '2-item list, HString glue' => [new HArray(['apple', 'banana']), new HString(' '), new HString('apple banana')],
            '2-item dict, HString glue' => [new HArray(['a' => 'apple', 'b' => 'banana']), new HString(' '), new HString('apple banana')],
        ];
    }
}
