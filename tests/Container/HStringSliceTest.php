<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringSliceTest extends TestCase
{
    /**
     * @dataProvider providerFirstPartOfTypesOfStringUsingSlice
     *
     * @param HString $target
     * @param HString $expected
     */
    public function testGetFirstPartOfTypesOfStringUsingSlice(HString $target, $expected): void
    {
        $this->assertEquals($expected, $target->slice(0, 4));
    }

    public function providerFirstPartOfTypesOfStringUsingSlice(): array
    {
        return [
            'String' => [new HString('foobar'), 'foob'],
            'ASCII HString' => [new HString('foobar'), new HString('foob')],
            'UTF-8 HString' => [new HString('ɹɐqooɟ'), new HString('ɹɐqo')],
        ];
    }

    /**
     * @dataProvider providerLastPartOfTypesOfStringUsingSlice
     *
     * @param HString $target
     * @param HString $expected
     */
    public function testGetLastPartOfTypesOfStringUsingSlice(HString $target, $expected): void
    {
        $this->assertEquals($expected, $target->slice(-4));
    }

    public function providerLastPartOfTypesOfStringUsingSlice(): array
    {
        return [
            'HString' => [new HString('foobar'), new HString('obar')],
            'UTF-8 HString' => [new HString('ɹɐqooɟ'), new HString('qooɟ')],
        ];
    }

    /**
     * @dataProvider middlePartOfStringProvider
     *
     * @param HString $target
     * @param HString $expected
     * @param integer $start
     * @param integer $finish
     */
    public function testGetMiddlePartOfTypesOfStringUsingSlice(HString $target, HString $expected, $start, $finish): void
    {
        $this->assertEquals($expected, $target->slice($start, $finish));
    }

    public function middlePartOfStringProvider(): array
    {
        return [
            'ASCII HString: Negative finish, middle' => [new HString('foobar'), new HString('ob'), 2, -2],
            'ASCII HString: Negative start & finish, middle' => [new HString('foobar'), new HString('ob'), -4, -2],
            'ASCII HString: normal middle' => [new HString('foobar'), new HString('ob'), 2, 2],
            'ASCII HString: null finish' => [new HString('foobar'), new HString('obar'), 2, null],
            'ASCII HString: overflow finish' => [new HString('foobar'), new HString('obar'), 2, 2000],
            'UTF-8 HString: Negative finish, middle' => [new HString('ɹɐqooɟ'), new HString('qo'), 2, -2],
            'UTF-8 HString: Negative start & finish, middle' => [new HString('ɹɐqooɟ'), new HString('qo'), -4, -2],
            'UTF-8 HString: normal middle' => [new HString('ɹɐqooɟ'), new HString('qo'), 2, 2],
            'UTF-8 HString: null finish' => [new HString('ɹɐqooɟ'), new HString('qooɟ'), 2, null],
            'UTF-8 HString: overflow finish' => [new HString('ɹɐqooɟ'), new HString('qooɟ'), 2, 2000],
        ];
    }
}
