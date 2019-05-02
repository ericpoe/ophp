<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringFilterTest extends TestCase
{
    /** @var HString */
    protected $aString;

    /** @var HString */
    protected $utf8String;

    protected function setUp(): void
    {
        $this->aString = new HString('foobar');
        $this->utf8String = new HString('ɹɐqooɟ');
    }

    public function testStringDefaultFilter(): void
    {
        $strangeString = $this->aString->insert('0', 3);
        $default = $strangeString->filter();
        $this->assertEquals('foobar', $default->toString(), 'Filter with defaults');

        $strangeString = $this->utf8String->insert('0', 3);
        $default = $strangeString->filter();
        $this->assertEquals('ɹɐqooɟ', $default->toString(), 'Filter with defaults');
    }

    public function testStringValuesFilter(): void
    {
        $removeVowels = function ($letter) {
            $vowels = new HString('aeiouᴉǝɐ');

            return !$vowels->contains($letter);
        };

        $consonants = $this->aString->filter($removeVowels);
        $this->assertEquals('fbr', $consonants->toString(), 'Filter by Value');

        $consonants = $this->utf8String->filter($removeVowels);
        $this->assertEquals('ɹqɟ', $consonants->toString(), 'Filter by Value');
    }

    public function testStringKeyFilter(): void
    {
        $removeOdd = function ($key) {
            return $key % 2;
        };

        $flag = HString::USE_KEY;

        $even = $this->aString->filter($removeOdd, $flag);
        $utf8Even = $this->utf8String->filter($removeOdd, $flag);
        $this->assertEquals('obr', $even->toString(), 'Filter by Key');
        $this->assertEquals('ɐoɟ', $utf8Even->toString(), 'Filter by Key');

        $even = $this->aString->filter(function ($key) {
            return $key % 2;
        }, $flag);
        $this->assertEquals('obr', $even->toString(), 'Filter by Key');
    }

    public function testStringValueAndKeyFilter(): void
    {

        $alpha = new HString('abcdefghijklmnopqrstuvwxyz');
        $evenAlpha = $alpha->filter(function ($key) {
            return $key % 2;
        }, HString::USE_KEY);

        $thingBoth = function ($letter, $key) use ($evenAlpha) {
            if ($evenAlpha->contains($letter)) {
                return true;
            }

            return $key % 2;
        };

        $flag = HString::USE_BOTH;
        $funky = $this->aString->filter($thingBoth, $flag);
        $this->assertEquals('fobr', $funky->toString(), 'Filter by both Value & Key');
        $utf8Funky = $this->utf8String->filter($thingBoth, $flag);
        $this->assertEquals('ɐoɟ', $utf8Funky->toString(), 'Filter by both Value & Key');
    }

    public function testInvalidFilterFlag(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid flag name');

        $flag = 'bad_flag';

        $this->aString->filter(function ($key) {
            return $key % 2;
        }, $flag);
    }
}
