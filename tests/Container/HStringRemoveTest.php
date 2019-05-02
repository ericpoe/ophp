<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringRemoveTest extends TestCase
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

    public function testTypesOfStringRemove(): void
    {
        $newString = $this->aString->remove('o');
        $this->assertEquals(new HString('fobar'), $newString);

        $newString = $this->utf8String->remove('o');
        $this->assertEquals(new HString('ɹɐqoɟ'), $newString);
    }
}
