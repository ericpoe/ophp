<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringContainsTest extends TestCase
{
    /**
     * @dataProvider stringContainsProvider
     *
     * @param HString $target
     * @param string|HString $checkString
     * @param bool $expectedBool
     */
    public function testTypesOfStringInFoobar(HString $target, $checkString, bool $expectedBool): void
    {
        $var = $target->contains($checkString);
        $expectedBool ? $this->assertTrue($var) : $this->assertFalse($var);
    }

    public function stringContainsProvider(): array
    {
        $aString = new HString('foobar');
        $utf8String = new HString('ɹɐqooɟ');
        return [
            'ASCII: String known-present' => [$aString, 'oob', true],
            'ASCII: String known-missing' => [$aString, 'baz', false],
            'ASCII: String letter known-present' => [$aString, 'b', true],
            'ASCII: String letter known-missing' => [$aString, 'z', false],
            'ASCII: HString known-present' => [$aString, new HString('oob'), true],
            'ASCII: HString letter known-present' => [$aString, new HString('b'), true],
            'ASCII: HString known-missing' => [$aString, new HString('baz'), false],
            'ASCII: HString letter known-missing' => [$aString, new HString('z'), false],
            'ASCII: Integer known-missing' => [$aString, 42, false],
            'UTF-8: String known-present' => [$utf8String, 'ɐqo', true],
            'UTF-8: String known-missing' => [$utf8String, 'zɐq', false],
            'UTF-8: String letter known-present' => [$utf8String, 'q', true],
            'UTF-8: String letter known-missing' => [$utf8String, 'z', false],
            'UTF-8: HString known-present' => [$utf8String, new HString('ɐqo'), true],
            'UTF-8: HString letter known-present' => [$utf8String, new HString('q'), true],
            'UTF-8: HString known-missing' => [$utf8String, new HString('zɐq'), false],
            'UTF-8: HString letter known-missing' => [$utf8String, new HString('z'), false],
            'UTF-8: Integer known-missing' => [$utf8String, 42, false],
        ];
    }

    public function testObjectWithString(): void
    {
        $date = new \DateTime('now');
        $timeStamp = $date->format('c');
        $timeSentence = new HString(sprintf('I have %s in me.', $timeStamp));
        $this->assertTrue($timeSentence->contains($date->format('c')));

        $obj = new class() {
            public function __toString(): string
            {
                return sprintf("I'm a string");
            }
        };

        $sampleString = "I'm a string";
        $objSentence = new HString(sprintf('I have %s in me.', $sampleString));
        $this->assertTrue($objSentence->contains((string) $obj));
    }

    /**
     * @dataProvider badTypesOfStringInFoobar
     * @param HString $item
     * @param string $exceptionMsg
     * @throws \InvalidArgumentException
     */
    public function testBadTypesOfStringInFoobar($item, string $exceptionMsg): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage($exceptionMsg);

        (new HString('foobar'))->contains($item);
    }

    public function badTypesOfStringInFoobar(): array
    {
        return [
            'DateTime' => [
                new \DateTime(),
                'DateTime cannot be converted to a string; it cannot be used as a search value within an HString'
            ],
            'SplDoublyLinkedList' => [
                new \SplDoublyLinkedList(),
                'SplDoublyLinkedList cannot be converted to a string; it cannot be used as a search value within an HString'
            ],
        ];
    }
}
