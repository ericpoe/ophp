<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringReduceTest extends TestCase
{
    /** @var HString */
    protected $aString;

    protected function setUp(): void
    {
        $this->aString = new HString('foobar');
    }

    public function testReduce(): void
    {
        $fn = function ($carry, $item) {
            $value = (ord(mb_strtolower($item)) - 64);
            return $carry + $value;
        };

        $this->assertEquals(249, $this->aString->reduce($fn));
    }

    public function testHStringReduce(): void
    {
        $encode = function ($carry, $item) {
            $value = (ord($item) % 26) + 97;
            $carry .= chr($value);

            return $carry;
        };

        $decode = function ($carry, $item) {
            $value = ((ord($item) + 14) % 26) + 97;
            $carry .= chr($value);

            return $carry;
        };

        $codedMessage = new HString('yhhutk');

        $this->assertEquals($codedMessage, $this->aString->reduce($encode));
        $this->assertEquals('foobar', $codedMessage->reduce($decode));
        $this->assertInstanceOf(HString::class, $this->aString->reduce($encode));
    }

    /**
     * @dataProvider stringReduceAsArrayTypeProvider
     */
    public function testStringReduceAsArrayTypeReturnsHArray(callable $freq, string $message): void
    {
        $this->assertInstanceOf(HArray::class, $this->aString->reduce($freq), $message);
    }

    public function stringReduceAsArrayTypeProvider(): array
    {
        $freqArray = function ($frequency, $letter) {
            if (!isset($frequency[$letter])) {
                $frequency[$letter] = 0;
            }

            $frequency[$letter]++;

            return $frequency;
        };

        $freqArrayObject = function ($frequency, $letter) {
            if (!isset($frequency[$letter])) {
                $frequency[$letter] = 0;
            }

            $frequency = new \ArrayObject($frequency);

            $frequency[$letter]++;

            return $frequency;
        };

        $freqHArray = function ($frequency, $letter) {
            if (!isset($frequency[$letter])) {
                $frequency[$letter] = 0;
            }

            $frequency = new HArray($frequency);

            $frequency[$letter]++;

            return $frequency;
        };

        return [
            'Array' => [$freqArray, 'An Array'],
            'ArrayObject' => [$freqArrayObject, 'An ArrayObject'],
            'HArray' => [$freqHArray, 'An HArray'],
        ];
    }

    /**
     * @dataProvider stringReduceWithInitialValueProvider
     */
    public function testStringReduceWithInitialValue(HString $hString, string $initial, string $expected): void
    {
        $what = function ($carry, $item) {
            $carry .= $item;

            return $carry;
        };

        $this->assertEquals($expected, $hString->reduce($what, $initial));
    }

    public function stringReduceWithInitialValueProvider(): array
    {
        return [
            'Empty HString' => [new HString(), 'alone', 'alone'],
            'HString' => [new HString('present'), 'The ', 'The present'],
        ];
    }
}
