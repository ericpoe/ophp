<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HArray;
use PHPUnit\Framework\TestCase;

class HArrayContainsTest extends TestCase
{
    /** @var HArray */
    private $arrList;

    /** @var HArray */
    private $arrDict;

    protected function setUp(): void
    {
        $this->arrList = new HArray(['apple', 'bobble', 'cobble', 'dobble']);
        $this->arrDict = new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble']);
    }

    /**
     * @dataProvider arrayContainsProvider
     *
     * @param string $type
     * @param string|int $checkThing
     * @param bool $expected
     */
    public function testContainsStringTypeInHArray(string $type, $checkThing, bool $expected): void
    {
        if ('list' === $type) {
            $contains = $this->arrList->contains($checkThing);
        } else {
            $contains = $this->arrDict->contains($checkThing);
        }
        $expected ? $this->assertTrue($contains) : $this->assertFalse($contains);
    }

    public function arrayContainsProvider(): array
    {
        return [
            '1st item in list' => ['list', 'apple', true],
            '3rd item in list' => ['list', 'cobble', true],
            'String not in list' => ['list', 'fobble', false],
            'Int not in list' => ['list', 3, false],
            '1st item in dictionary' => ['dict', 'apple', true],
            '3rd item in dictionary' => ['dict', 'cobble', true],
            'String not in dictionary' => ['dict', 'fobble', false],
            'Int not in dictionary' => ['dict', 3, false],
        ];
    }

    public function testContainsObjectTypeInHArray(): void
    {
        $list = $this->arrList->append(new \SplDoublyLinkedList());

        $this->assertTrue($list->contains(new \SplDoublyLinkedList()), 'SplDoublyLinkedList should be in the list');
        $this->assertFalse($list->contains(new \DateTime()), 'DateTime should not be in the list');
    }
}
