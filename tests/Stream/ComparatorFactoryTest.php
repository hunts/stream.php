<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class: {@see ComparatorFactory}
 */
class ComparatorFactoryTest extends TestCase
{
    public function testCreate()
    {
        $c = ComparatorFactory::create(function($a, $b) {
            return $a <=> $b;
        });

        $this->assertSame(-1, $c->compare(1, 2));
        $this->assertSame(0, $c->compare(1, 1));
        $this->assertSame(1, $c->compare(2, 1));
    }

    /**
     * @param string|null $a
     * @param string|null $b
     * @param bool $isCaseSensitive
     * @param int $flags
     * @param int $expectedResult
     *
     * @dataProvider dataProviderOfStringComparator
     */
    public function testStringComparator(?string $a, ?string $b, bool $isCaseSensitive, int $flags, int $expectedResult)
    {
        $c = ComparatorFactory::stringComparator($isCaseSensitive, $flags);
        $this->assertSame($expectedResult, $c->compare($a, $b));
    }

    public function dataProviderOfStringComparator()
    {
        return [
            ['A', 'b', false, Comparator::NULL_AS_ZERO, -1],
            ['a', 'b', false, Comparator::NULL_AS_ZERO, -1],
            ['a', 'a', false, Comparator::NULL_AS_ZERO, 0],
            ['a', 'A', false, Comparator::NULL_AS_ZERO, 0],
            ['b', 'a', false, Comparator::NULL_AS_ZERO, 1],
            ['B', 'a', false, Comparator::NULL_AS_ZERO, 1],
            ['A', 'b', true, Comparator::NULL_AS_ZERO, -1],
            ['a', 'b', true, Comparator::NULL_AS_ZERO, -1],
            ['a', 'B', true, Comparator::NULL_AS_ZERO, 1],
            ['a', 'a', true, Comparator::NULL_AS_ZERO, 0],
            ['a', 'A', true, Comparator::NULL_AS_ZERO, 1],
            ['A', 'a', true, Comparator::NULL_AS_ZERO, -1],
            ['b', 'a', true, Comparator::NULL_AS_ZERO, 1],
            ['b', 'A', true, Comparator::NULL_AS_ZERO, 1],
            ['B', 'a', true, Comparator::NULL_AS_ZERO, -1],
        ];
    }

    /**
     * @param float|null $a
     * @param float|null $b
     * @param float $epsilon
     * @param int $flags
     * @param int $expectedResult
     *
     * @dataProvider dataProviderOfFloatComparator
     */
    public function testFloatComparator(?float $a, ?float $b, float $epsilon, int $flags, int $expectedResult)
    {
        $c = ComparatorFactory::floatComparator(0.01, $flags);
        $this->assertSame($expectedResult, $c->compare($a, $b));
    }

    public function dataProviderOfFloatComparator()
    {
        return [
            [1.01, 1.02, 0.01, Comparator::NULL_AS_ZERO, -1],
            [1.01, 1.01, 0.01, Comparator::NULL_AS_ZERO, 0],
            [1.009, 1.012, 0.01, Comparator::NULL_AS_ZERO, 0],
            [1.012, 1.009, 0.01, Comparator::NULL_AS_ZERO, 0],
            [1.02, 1.01, 0.01, Comparator::NULL_AS_ZERO, 1],
        ];
    }

    /**
     * @param int|null $a
     * @param int|null $b
     * @param int $flags
     * @param int $expectedResult
     *
     * @dataProvider dataProviderOfIntComparator
     */
    public function testIntComparator(?int $a, ?int $b, int $flags, int $expectedResult)
    {
        $c = ComparatorFactory::intComparator($flags);
        $this->assertSame($expectedResult, $c->compare($a, $b));
    }

    public function dataProviderOfIntComparator()
    {
        return [
            [1, 2, Comparator::NULL_AS_ZERO, -1],
            [1, 1, Comparator::NULL_AS_ZERO, 0],
            [2, 1, Comparator::NULL_AS_ZERO, 1],
            [null, null, Comparator::NULL_AS_ZERO, 0],
            [null, 0, Comparator::NULL_AS_ZERO, 0],
            [null, 1, Comparator::NULL_AS_ZERO, -1],
            [0, null, Comparator::NULL_AS_ZERO, 0],
            [1, null, Comparator::NULL_AS_ZERO, 1],
            [null, null, Comparator::NULL_LT_ZERO, 0],
            [null, 0, Comparator::NULL_LT_ZERO, -1],
            [0, null, Comparator::NULL_LT_ZERO, 1],
        ];
    }
}
