<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\Container\ElementNotFoundException;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringLocateTest extends TestCase
{
    /**
     * @dataProvider stringLocateProvider()
     *
     * @param HString $target
     * @param int $expected
     * @throws ElementNotFoundException
     */
    public function testLocateTypesOfStringInFoobar(HString $target, $checkString, int $expected): void
    {
        $checkString = $checkString instanceof HString ? (string) $checkString : $checkString;
        $var = $target->locate($checkString);
        $this->assertEquals($expected, $var);
    }

    public function stringLocateProvider(): array
    {
        $aString = new HString('foobar');
        $utf8String = new HString('ɹɐqooɟ');

        return [
            'ASCII: String known-present' => [$aString, 'oob', 1],
            'ASCII: HString known-present' => [$aString, new HString('oob'), 1],
            'UTF-8: String known-present' => [$utf8String, 'ɐqo', 1],
            'UTF-8: HString known-present' => [$utf8String, new HString('ɐqo'), 1],
        ];
    }

    public function testObjectWithString(): void
    {
        $date = new \DateTime('now');
        $timeStamp = $date->format('c');
        $timeSentence = new HString(sprintf('I have %s in me.', $timeStamp));
        $this->assertEquals(7, $timeSentence->locate($date->format('c')));

        $obj = new class() {
            public function __toString(): string
            {
                return sprintf("I'm a string");
            }
        };

        $sampleString = "I'm a string";
        $objSentence = new HString(sprintf('I have %s in me.', $sampleString));
        $this->assertEquals(7, $objSentence->locate((string) $obj));
    }

    /**
     * @dataProvider stringBadLocateProvider()
     *
     * @param HString $checkString
     * @param string $message
     * @throws ElementNotFoundException
     */
    public function testCannotLocateTypesOfStringInFoober($checkString, string $message): void
    {
        $this->expectException(ElementNotFoundException::class);
        $this->expectExceptionMessage($message);

        (new HString('foobar'))->locate($checkString);
    }

    public function stringBadLocateProvider(): array
    {
        return [
            'String known-missing' => ['baz', 'Element could not be found: baz'],
            'HString known-missing' => [new HString('baz'), 'Element could not be found: baz'],
            'Integer known-missing' => [42, 'Element could not be found: 42'],
            'HString integer known-missing' => [new HString('42'), 'Element could not be found: 42'],
        ];
    }

    /**
     * @dataProvider badLocateTypesOfStringInFoobarProvider
     *
     * @param HString $item
     * @param string $exceptionMsg
     * @throws ElementNotFoundException
     */
    public function testBadLocateTypesOfStringInFoobar($item, string $exceptionMsg): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage($exceptionMsg);

        (new HString('foobar'))->locate($item);
    }

    public function badLocateTypesOfStringInFoobarProvider(): array
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
