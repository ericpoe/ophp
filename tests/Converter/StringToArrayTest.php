<?php
declare(strict_types=1);

namespace Haystack\Tests\Converter;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class StringToArrayTest extends TestCase
{
    public function testHStringToArray(): void
    {
        $emptyString = new HString();
        $expected = [];
        $this->assertEquals($expected, $emptyString->toArray());

        $aString = new HString('foobar');
        $expected = ['f', 'o', 'o', 'b', 'a', 'r'];
        $this->assertEquals($expected, $aString->toArray());
    }

    /**
     * @dataProvider stringToHArrayProvider
     *
     * @param HString $hString
     * @param string|null $delim
     * @param int|null $limit
     * @param HArray $expected
     */
    public function testStringToHArray(HString $hString, ?string $delim, ?int $limit, HArray $expected): void
    {
        $this->assertEquals($expected, $hString->toHArray($delim, $limit));
    }

    public function stringToHArrayProvider(): array
    {
        $jabberwocky = "'Twas brillig and the slithy toves";
        $jabberwockyColon = "'Twas:brillig:and:the:slithy:toves";
        $jabberUTF8 = 'sǝʌoʇ ʎɥʇᴉls ǝɥʇ puɐ ƃᴉllᴉɹq sɐʍ┴,';
        $jabberUTF8_short = 'ƃᴉllᴉɹq sɐʍ┴,';

        return [
            'Empty String' => [new HString(), null, null, new HArray()],
            'String of integers with null delims' => [new HString('1 2 3 4 5'), null, null, new HArray([1, ' ', 2, ' ', 3, ' ', 4, ' ', 5])],
            'String of integers with zero delims' => [new HString('102030405'), 0, null, new HArray(['1', '2', '3', '4', '5'])],
            'String of integers with string-zero delims' => [new HString('102030405'), '0', null, new HArray(['1', '2', '3', '4', '5'])],
            'String of integers with blank string delims' => [new HString('1 2 3 4 5'), null, null, new HArray([1, ' ', 2, ' ', 3, ' ', 4, ' ', 5])],
            'String of integers with space delims' => [new HString('1 2 3 4 5'), ' ', null, new HArray([1, 2, 3, 4, 5])],
            'String of integers with comma delims' => [new HString('1, 2, 3, 4, 5'), ',', null, new HArray([1, 2, 3, 4, 5])],
            'String of integers with non-existent delims' => [new HString('1, 2, 3, 4, 5'), 'foo', null, new HArray(['1, 2, 3, 4, 5'])],
            'String of integers with HString space delims' => [new HString('1 2 3 4 5'), new HString(' '), null, new HArray([1, 2, 3, 4, 5])],
            'String of integers with HString comma delims' => [new HString('1, 2, 3, 4, 5'), new HString(','), null, new HArray([1, 2, 3, 4, 5])],
            'String of words with null delims' => [new HString($jabberwocky), null, null, new HArray(["'", 'T', 'w', 'a', 's', ' ', 'b', 'r', 'i', 'l', 'l', 'i', 'g', ' ', 'a', 'n', 'd', ' ', 't', 'h', 'e', ' ', 's', 'l', 'i', 't', 'h', 'y', ' ', 't', 'o', 'v', 'e', 's'])],
            'String of UTF-8 words with null delims' => [new HString($jabberUTF8_short), null, null, new HArray(['ƃ', 'ᴉ', 'l', 'l', 'ᴉ', 'ɹ', 'q', ' ', 's', 'ɐ', 'ʍ', '┴', ','])],
            'String of words with space delims' => [new HString($jabberwocky), ' ', null, new HArray(["'Twas", 'brillig', 'and', 'the', 'slithy', 'toves'])],
            'String of UTF-8 words with space delims' => [new HString($jabberUTF8), ' ', null, new HArray(['sǝʌoʇ', 'ʎɥʇᴉls', 'ǝɥʇ', 'puɐ', 'ƃᴉllᴉɹq', 'sɐʍ┴,'])],
            'String of words with colon delims' => [new HString($jabberwockyColon), ':', null, new HArray(["'Twas", 'brillig', 'and', 'the', 'slithy', 'toves'])],
            'String of integers with space delims & limit' => [new HString('1 2 3 4 5'), ' ', 3, new HArray([1, 2, '3 4 5'])],
            'String of integers with comma delims & limit' => [new HString('1, 2, 3, 4, 5'), ', ', 3, new HArray([1, 2, '3, 4, 5'])],
            'String of words with space delims & limit' => [new HString($jabberwocky), ' ', 3, new HArray(["'Twas", 'brillig', 'and the slithy toves'])],
            'String of UTF-8 words with space delims & limit' => [new HString($jabberUTF8), ' ', 3, new HArray(['sǝʌoʇ', 'ʎɥʇᴉls', 'ǝɥʇ puɐ ƃᴉllᴉɹq sɐʍ┴,'])],
            'String of words with colon delims & limit' => [new HString($jabberwockyColon), ':', 3, new HArray(["'Twas", 'brillig', 'and:the:slithy:toves'])],
        ];
    }
}
