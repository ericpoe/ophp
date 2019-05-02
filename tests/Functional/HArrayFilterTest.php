<?php
declare(strict_types=1);

namespace Haystack\Tests\Functional;

use Haystack\HArray;
use Haystack\HString;
use PHPUnit\Framework\TestCase;

class HArrayFilterTest extends TestCase
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

    public function testArrayDefaultFilter(): void
    {
        $this->arrList = $this->arrList->insert(0, 2);
        $this->assertEquals(new HArray(['apple', 'bobble', 'cobble', 'dobble']), $this->arrList->filter(), 'List - Default Filter');
        $this->assertEquals(new HArray(['a' => 'apple', 'b' => 'bobble', 'c' => 'cobble', 'd' => 'dobble']), $this->arrDict->filter(), 'Dict - Default Filter');

    }

    public function testArrayWithValueFilter(): void
    {
        $vowel = function ($word) {
            $vowels = new HString('aeiou');

            return $vowels->contains($word[0]);
        };

        $this->assertEquals(new HArray(['apple']), $this->arrList->filter($vowel), 'List - Value Filter');
        $this->assertEquals(new HArray(['a' => 'apple']), $this->arrDict->filter($vowel), 'Dict - Value Filter');
    }

    public function testArrayWithKeyFilter(): void
    {
        $vowelKey = function ($key) {
            $vowels = new HString('aeoiu');

            return $vowels->contains($key);
        };

        $flag = HArray::USE_KEY;
        $arr = new HArray(['a' => 'bobble', 'b' => 'apple', 'c' => 'cobble']);
        $this->assertEquals(new HArray(['a' => 'bobble']), $arr->filter($vowelKey, $flag), 'Dict - Key Filter');
    }

    public function testArrayWithArrayAndKeyFilter(): void
    {
        $vowel_both = function ($value, $key) {
            $vowels = new HString('aeiou');

            if ($vowels->contains($value[0])) {
                return true;
            }

            return $vowels->contains($key);
        };

        $flag = HArray::USE_BOTH;
        $arr = new HArray(['a' => 'bobble', 'b' => 'apple', 'c' => 'cobble']);
        $this->assertEquals(new HArray(['b' => 'apple', 'a' => 'bobble']), $arr->filter($vowel_both, $flag), 'Dict - Value & Key Filter');
    }

    public function testBadArrayFilterFlag(): void
    {
        $vowel = function ($word) {
            $vowels = new HString('aeiou');

            return $vowels->contains($word[0]);
        };

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid flag name');

        $this->arrList->filter($vowel, 'boooth');
    }
}
