<?php
declare(strict_types=1);

namespace Haystack\Tests\Converter;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class StringToStringTest extends TestCase
{
    /**
     * @dataProvider stringToStringCastProvider
     */
    public function testStringToStringCast(HString $arr, string $expected): void
    {
        $this->assertEquals($expected, (string) $arr);
    }

    public function StringToStringCastProvider(): array
    {
        return [
            'Empty Array' => [new HString(), ''],
            '1 char string' => [new HString('a'), 'a'],
            '2 char string' => [new HString('ab'), 'ab'],
            '1 word string' => [new HString('apple'), 'apple'],
            '2 word string' => [new HString('apple banana'), 'apple banana'],
        ];
    }

    /**
     * @dataProvider stringToStringWithGlueProvider
     */
    public function testStringToStringWithGlue(HString $arr, ?string $glue, string $expected): void
    {
        if (null === $glue) {
            $glue = '';
        }

        $this->assertEquals($expected, $arr->toString($glue));
    }

    public function StringToStringWithGlueProvider(): array
    {
        return [
            'Empty Array, null glue' => [new HString(), null, ''],
            '1 char string, null glue' => [new HString('a'), null, 'a'],
            '2 char string, null glue' => [new HString('ab'), null, 'ab'],
            '1 word string, null glue' => [new HString('apple'), null, 'apple'],
            '2 word string, null glue' => [new HString('apple banana'), null, 'apple banana'],
            '1 char string, space glue' => [new HString('a'), ' ', 'a'],
            '2 char string, space glue' => [new HString('ab'), ' ', 'a b'],
            '1 word string, space glue' => [new HString('apple'), ' ', 'a p p l e'],
            '2 word string, space glue' => [new HString('apple banana'), ' ', 'a p p l e   b a n a n a'],
        ];
    }
}
