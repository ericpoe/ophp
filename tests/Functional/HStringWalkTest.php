<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HStringWalkTest extends TestCase
{
    /** @var HString */
    protected $aString;

    protected function setUp(): void
    {
        $this->aString = new HString('foobar');
    }

    public function testStringWalk(): void
    {
        $this->aString->walk(function ($letter, $key) {
            return $this->aString[$key] = strtoupper($letter);
        });

        $this->assertEquals('FOOBAR', $this->aString->toString());
    }
}
