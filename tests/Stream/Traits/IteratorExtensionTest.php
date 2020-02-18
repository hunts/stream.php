<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Stream\Traits;

use PHPUnit\Framework\TestCase;
use Stream\ComparatorFactory;
use Stream\IteratorClass;

/**
 * Test cases for trait: {@see IteratorExtension}
 */
class IteratorExtensionTest extends TestCase
{
    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function tesStreamify(IteratorClass $it, $first, $last, int $count, callable $predicate = NULL)
    {
        try {
            $this->assertSame($it, $it->streamify());
        }catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function tesToArray(IteratorClass $it, $first, $last, int $count, callable $predicate = NULL)
    {
        $arr = $it->toArray($predicate);

        if ($count > 0) {
            $this->assertSame($first, $arr[0]);
            $this->assertSame($last, $arr[$count - 1]);
        } else {
            $this->assertEmpty($arr);
        }
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function testAny(IteratorClass $it, $first, $last, int $count, callable $predicate = NULL)
    {
        $this->assertEquals($count > 0, $it->any($predicate));
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function testAll(IteratorClass $it, $first, $last, int $count, callable $predicate = NULL)
    {
        $this->assertEquals($count === $it->count(), $it->all($predicate));
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function testFirst(IteratorClass $it, $first, $last, $count, callable $predicate = NULL)
    {
        $this->assertEquals($first, $it->first($predicate));
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function testLast(IteratorClass $it, $first, $last, $count, callable $predicate = null)
    {
        $this->assertEquals($last, $it->last($predicate));
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function testCount(IteratorClass $it, $first, $last, $count, callable $predicate = null)
    {
        $this->assertEquals($count, $it->count($predicate));
    }

    /**
     * @param IteratorClass $it
     * @param mixed $first
     * @param mixed $last
     * @param int $count
     * @param callable $predicate
     *
     * @dataProvider dataProvider
     */
    public function testEach(IteratorClass $it, $first, $last, $count, callable $predicate = null)
    {
        $it->each(function ($item) use (&$count) {
            $count--;
        }, $predicate);

        $this->assertEquals(0, $count);
    }

    /**
     * @param IteratorClass $it
     * @param number $min
     * @param number $max
     * @param number $avg
     * @param number $sum
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testMin(IteratorClass $it, $min, $max, $avg, $sum, callable $predicate = null)
    {
        $this->assertEquals($min, $it->min(ComparatorFactory::intComparator(), $predicate));
    }

    /**
     * @param IteratorClass $it
     * @param number $min
     * @param number $max
     * @param number $avg
     * @param number $sum
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testMax(IteratorClass $it, $min, $max, $avg, $sum, callable $predicate = null)
    {
        $this->assertEquals($max, $it->max(ComparatorFactory::intComparator(), $predicate));
    }

    /**
     * @param IteratorClass $it
     * @param number $min
     * @param number $max
     * @param number $avg
     * @param number $sum
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testAverage(IteratorClass $it, $min, $max, $avg, $sum, callable $predicate = null)
    {
        $this->assertEquals($avg, $it->average($predicate));
    }

    /**
     * @param IteratorClass $it
     * @param number $min
     * @param number $max
     * @param number $avg
     * @param number $sum
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testSum(IteratorClass $it, $min, $max, $avg, $sum, callable $predicate = null)
    {
        $this->assertEquals($sum, $it->sum($predicate));
    }

    public function dataProvider()
    {
        $emptyIterator = new IteratorClass();

        $func = new IteratorClass(5);
        $func[0] = '0';
        $func[1] = null;
        $func[2] = 'x';
        $func[3] = 'xyz';
        $func[4] = 'kkx';

        return [
            [$emptyIterator, null, null, 0],
            [$func, '0', 'kkx', 5],
            [$func, 'x', 'kkx', 3, function ($item) { return strpos($item, 'x') !== false; }],
            [$func, null, null, 1, function ($item) { return $item === null; }],
            [$func, '0', 'kkx', 4, function ($item) { return $item !== null; }]
        ];
    }

    public function numberDataProvider()
    {
        $emptyIterator = new IteratorClass();

        $func = new IteratorClass(5);
        $func[0] = -2;
        $func[1] = 1;
        $func[2] = 7;
        $func[3] = 3;
        $func[4] = 4;

        return [
            [$emptyIterator, null, null, null, null],
            [$func, -2, 7, 2.6, 13],
            [$func, 1, 7, 3.75, 15, function ($item) { return $item > 0; }],
        ];
    }
}
