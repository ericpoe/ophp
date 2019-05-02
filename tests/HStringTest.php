<?php
declare(strict_types=1);

namespace Haystack\Tests;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringTest extends TestCase
{
    /** @var HString */
    protected $aString;

    /** @var  HString */
    protected $utf8String;

    protected function setUp(): void
    {
        $this->aString = new HString('foobar');
        $this->utf8String = new HString('ɹɐqooɟ');
    }

    public function testCreateEmptyString(): void
    {
        $emptyString = new HString();
        $this->assertEmpty($emptyString);
    }

    /**
     * @dataProvider stringOfThingsProvider
     *
     * @param mixed $item
     * @param string $expected
     */
    public function testCreateHStringOfThings($item, string $expected):void
    {
        $this->aString = new HString($item);
        $this->assertEquals($expected, $this->aString);
    }

    public function stringOfThingsProvider(): array
    {
        $timeStamp = new \DateTime();

        return [
            'Empty String' => [' ', ' '],
            'Simple string' => ['abc', 'abc'],
            'UTF-8 string' => ['ɹɐqooɟ', 'ɹɐqooɟ'],
            'DateTime formatted timestamp' => [$timeStamp->format('c'), $timeStamp->format('c')],
            'Blank string' => ['', ''],
            'Null string' => [null, ''],
        ];
    }

    public function testSerialize(): void
    {
        $serialized = $this->aString->serialize();
        $this->assertEquals(serialize($this->aString->toString()), $serialized);

        $serialized = $this->utf8String->serialize();
        $this->assertEquals(serialize($this->utf8String->toString()), $serialized);
    }

    /**
     * @dataProvider unserializeProvider
     */
    public function testUnserialize(string $str, HString $expected): void
    {
        $this->aString->unserialize($str);
        $this->assertEquals($expected, $this->aString);
    }

    public function unserializeProvider(): array
    {
        return [
            'String' => [serialize(new HString('foobar')), new HString('foobar')],
            'String with spaces' => [serialize('The quick brown fox jumps'), new HString('The quick brown fox jumps')],
            'UTF-8 string' => [serialize('ɹɐqooɟ'), new HString('ɹɐqooɟ')],
            'Null string' => [serialize(null), new HString()],
        ];
    }

    /**
     * @dataProvider badUnserializeProvider
     * @param mixed $item
     * @param string $exceptionMsg
     */
    public function testBadUnserialize($item, string $exceptionMsg): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage($exceptionMsg);

        $this->aString->unserialize($item);
    }

    public function badUnserializeProvider(): array
    {
        return [
            'Unserialized HString' => [new HString('The quick brown fox'), "HString cannot unserialize a Haystack\\HString"],
            'DateTime object' => [new \DateTime(), 'HString cannot unserialize a DateTime'],
        ];
    }

    public function testIteratorNext(): void
    {
        $this->aString->next();
        $this->assertEquals('o', $this->aString->current());

        $this->utf8String->next();
        $this->assertEquals('ɐ', $this->utf8String->current());
    }

    /**
     * @dataProvider iteratorValidProvider
     */
    public function testIteratorValid(HString $toIterate): void
    {
        $toIterate->next(); // "o"
        $toIterate->next(); // "o"
        $toIterate->next(); // "b"
        $toIterate->next(); // "a"
        $toIterate->next(); // "r"
        $this->assertTrue($toIterate->valid());
        $toIterate->next(); // uninitialized string offset
        $this->assertFalse($toIterate->valid());
    }

    public function iteratorValidProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar')],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ')],
        ];
    }

    /**
     * @dataProvider iteratorRewindProvider
     */
    public function testIteratorRewind(HString $toRewind, string $expected): void
    {
        $toRewind->next(); // "o"
        $toRewind->next(); // "o"
        $toRewind->next(); // 'b"

        $toRewind->rewind(); // back to "f"
        $this->assertEquals($expected, $toRewind->current());

        $toRewind->rewind(); // before the first char!
        $this->assertEquals($expected, $toRewind->current());
    }

    public function iteratorRewindProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar'), 'f'],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ'), 'ɹ'],
        ];
    }

    /**
     * @dataProvider iteratorKeyProvider
     */
    public function testIteratorKey(HString $toIterate, int $expected): void
    {
        $this->assertEquals(0, $toIterate->key());

        for ($i = 0; $i < $expected; $i++) {
            $toIterate->next();
        }

        $this->assertEquals($expected, $toIterate->key());
    }

    public function iteratorKeyProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar'), 3],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ'), 3],
        ];
    }

    /**
     * @dataProvider arrayStyleCountProvider
     */
    public function testArrayStyleCount(HString $toCount, int $expected): void
    {
        $this->assertEquals($expected, $toCount->count());
    }

    public function arrayStyleCountProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar'), 6],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ'), 6],
        ];
    }

    /**
     * @dataProvider arrayStyleOffsetExistsProvider
     */
    public function testArrayStyleOffsetExists(HString $stringToTest): void
    {
        $expectedLastOffset = $stringToTest->count() - 1;

        $this->assertTrue(isset($stringToTest[$expectedLastOffset]));
        $this->assertFalse(isset($stringToTest[$expectedLastOffset + 1]));
    }

    public function arrayStyleOffsetExistsProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar')],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ')],
        ];
    }

    /**
     * @dataProvider arrayStyleOffsetGetProvider
     */
    public function testArrayStyleOffsetGet(HString $stringToTest, int $offset, string $expected): void
    {
        $this->assertEquals($expected, $stringToTest[$offset]);
    }

    public function arrayStyleOffsetGetProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar'), 3, 'b'],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ'), 3, 'o'],
        ];
    }

    /**
     * @dataProvider arrayStyleOffsetSetProvider
     */
    public function testArrayStyleOffsetSet(HString $stringToTest, int $offset, string $char, HString $expected): void
    {
        $stringToTest[$offset] = $char;
        $this->assertEquals($expected, $stringToTest);
    }

    public function arrayStyleOffsetSetProvider(): array
    {
        return [
            'ASCII string to iterate' => [new HString('foobar'), 0, 'r', new HString('roobar')],
            'UTF-8 string to iterate' => [new HString('ɹɐqooɟ'), 0, 'ɟ', new HString('ɟɐqooɟ')],
        ];
    }

    public function testArrayStyleOffsetUnset(): void
    {
        unset($this->utf8String[3]);

        $binaryNull = chr(0x00);
        $this->assertEquals($binaryNull, $this->utf8String[3]);
    }

    public function testArrayStyleAccess(): void
    {
        $this->assertEquals('o', $this->aString[1]);
        $this->assertEquals('ɐ', $this->utf8String[1]);
    }

    public function testStringHead(): void
    {
        $this->assertEquals('f', (string) $this->aString->head());
        $this->assertEquals('ɹ', (string ) $this->utf8String->head());

        $emptyString = new HString();
        $this->assertEmpty((string) $emptyString->head());
    }

    public function testStringTail(): void
    {
        $this->assertEquals('oobar', (string) $this->aString->tail());

        $emptyString = new HString();
        $this->assertEmpty((string) $emptyString->tail());
    }

    /**
     * @dataProvider sumStringProvider
     */
    public function testStringSum(HString $hString, float $expected): void
    {
        $this->assertEquals($expected, $hString->sum());
    }

    public function sumStringProvider(): array
    {
        return [
            'Empty HString' => [new HString(), 0],
            'HString of chars' => [new HString((string) $this->aString), 0],
            'HString of chars & spaces' => [new HString('foo bar baz'), 0],
            'HString of comma-delimited ints' => [new HString('1, 2, 3, 4, 5, 6, 7, 8, 9, 10'), 55],
            'HString of comma-delimited ints & doubles' => [new HString('1.1, 2, 3, 4, 5, 6, 7, 8, 9, 10'), 55.1],
        ];
    }

    /**
     * @dataProvider productStringProvider
     */
    public function testStringProduct(HString $hString, float $expected): void
    {
        $this->assertEquals($expected, $hString->product());
    }

    public function productStringProvider(): array
    {
        return [
            'Empty HString' => [new HString(), 0],
            'HString of chars' => [new HString((string) $this->aString), 0],
            'HString of chars & spaces' => [new HString('foo bar baz'), 0],
            'HString of chars & ints' => [new HString('1, 2, 3, 4, 5, 6, 7, 8, 9, 10, apple'), 0],
            'HString of comma-delimited ints' => [new HString('1, 2, 3, 4, 5, 6, 7, 8, 9, 10'), 3628800],
            'HString of comma-delimited ints & doubles' => [new HString('1.1, 2, 3, 4, 5, 6, 7, 8, 9, 10'), 3991680],
        ];
    }
}
