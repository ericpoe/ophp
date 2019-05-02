<?php
declare(strict_types=1);

namespace Haystack\Tests\Container;

use Haystack\HArray;
use PHPUnit\Framework\TestCase;

class HArrayRemoveTest extends TestCase
{
    /** @var  HArray */
    private $arrList;

    /** @var  HArray */
    private $arrDict;

    protected function setUp(): void
    {
        $this->arrList = new HArray(['apple', 'bobble', 'cobble', 'dobble']);
        $this->arrDict = new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble']);
    }

    /**
     * @dataProvider arrayRemoveProvider
     *
     * @param string $type
     * @param string $value
     * @param HArray $expected
     */
    public function testArrayTypeRemove(string $type, string $value, HArray $expected): void
    {
        if ('list' === $type) {
            $newArr = $this->arrList->remove($value);
        } else {
            $newArr = $this->arrDict->remove($value);
        }

        $this->assertEquals($expected, $newArr);

    }

    public function arrayRemoveProvider(): array
    {
        return [
            'List: Basic list' => ['list', 'bobble', new HArray(['apple', 'cobble', 'dobble'])],
            'List: Basic list - item not found' => ['list', 'zobble', new HArray(['apple', 'bobble', 'cobble', 'dobble'])],
            'Basic dict' => ['dict', 'bobble', new HArray(['a' => 'apple', 'c' => 'cobble', 'd' => 'dobble'])],
            'Basic dict - item not found' => ['dict', 'zobble', new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble'])],
        ];
    }

    public function testArrayTypeRemoveObject(): void
    {
        $timestamp = new \DateTime();

        $arrList = $this->arrList->insert($timestamp, 2);
        $arrDict = $this->arrDict->insert($timestamp, 2);

        $this->assertEquals($this->arrList, $arrList->remove($timestamp), 'Object removed from list');
        $this->assertEquals($arrList, $arrList->remove(new \SplDoublyLinkedList()), 'Object not removed from list');
        $this->assertEquals($this->arrDict, $arrDict->remove($timestamp), 'Object removed from dict');
        $this->assertEquals($arrDict, $arrDict->remove(new \SplDoublyLinkedList()), 'Object not removed from dict');
    }

}
