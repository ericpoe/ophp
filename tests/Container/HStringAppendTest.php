<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringAppendTest extends TestCase
{
    /**
     * @dataProvider stringAppendProvider()
     *
     * @param HString $target
     * @param mixed $babyString
     * @param HString $expected
     */
    public function testTypesOfStringAppendToFoobar(HString $target, $babyString, HString $expected): void
    {
        $newString = $target->append($babyString);

        $this->assertEquals($expected->toString(), $newString);
    }

    public function stringAppendProvider(): array
    {
        $aString = new HString('foobar');
        $utf8String = new HString('ɹɐqooɟ');
        return [
            'ASCII HString: Append a normal string' => [
                $aString,
                'babyString' => 'baz',
                'expected' => new HString('foobarbaz')
            ],
            'ASCII HString: Append an HString' => [
                $aString,
                'babyString' => new HString('baz'),
                'expected' => new HString('foobarbaz')
            ],
            'ASCII HString: Append a UTF-8 HString' => [
                $aString,
                'babyString' => new HString('zɐq'),
                'expected' => new HString('foobarzɐq')
            ],
            'ASCII HString: Append an integer' => [
                $aString,
                'babyString' => 5,
                'expected' => new HString('foobar5')
            ],
            'ASCII HString: Append a double' => [
                $aString,
                'babyString' => 5.1,
                'expected' => new HString('foobar5.1')
            ],
            'UTF-8 HString: Append a normal string' => [
                $utf8String,
                'babyString' => 'baz',
                'expected' => new HString('ɹɐqooɟbaz')
            ],
            'UTF-8 HString: Append an HString' => [
                $utf8String,
                'babyString' => new HString('baz'),
                'expected' => new HString('ɹɐqooɟbaz')
            ],
            'UTF-8 HString: Append a UTF-8 HString' => [
                $utf8String,
                'babyString' => new HString('zɐq'),
                'expected' => new HString('ɹɐqooɟzɐq')
            ],
            'UTF-8 HString: Append an integer' => [
                $utf8String,
                'babyString' => 5,
                'expected' => new HString('ɹɐqooɟ5')
            ],
            'UTF-8 HString: Append a double' => [
                $utf8String,
                'babyString' => 5.1,
                'expected' => new HString('ɹɐqooɟ5.1')
            ],
        ];
    }

    public function testNonScalarTypeCannotBeAddedToFoobar(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Cannot concatenate an HString with a DateTime');

        /** @var String $badString */
        $badString = new \DateTime();

        (new HString('foobar'))->append($badString);
    }
}
