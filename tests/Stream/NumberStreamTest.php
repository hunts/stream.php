<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class: {@see NumberStream}
 */
class NumberStreamTest extends TestCase
{
    /**
     * @param number[] $numbers
     * @param number $sum
     * @param number $avg
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testSum(array $numbers, $sum, $avg, callable $predicate = null)
    {
        $s = NumberStream::from($numbers);
        $this->assertEquals($sum, $s->sum($predicate));
    }

    /**
     * @param number[] $numbers
     * @param number $sum
     * @param number $avg
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testAverage(array $numbers, $sum, $avg, callable $predicate = null)
    {
        $s = NumberStream::from($numbers);
        $this->assertEquals($avg, $s->average($predicate));
    }

    public function numberDataProvider()
    {
        return [
            [[-2, 1, 7, 3, 4], 13, 2.6],
            [[-2, 1, 7, 3, 4], 15, 3.75, function ($item) { return $item > 0; }],
            [[1, 7, 3, 4], 15, 3.75],
            [[], 0, 0],
        ];
    }
}
