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
use Stream\Stream;

/**
 * Test cases for trait: {@see IteratorExtension}
 */
class IteratorExtensionTest extends TestCase
{
    public function testStreamify()
    {
        $it = new IteratorClass();
        $s = $it->streamify();

        $this->assertInstanceOf(Stream::class, $s);
        $this->assertNotSame($it, $s);


        $this->assertSame($s, $s->streamify());
    }

    public function tesToArray()
    {
        $it = new IteratorClass(6);
        $it[0] = 0;
        $it[1] = 1;
        $it[2] = 2;
        $it[3] = 3;
        $it[4] = 4;
        $it[5] = 5;

        $arr = $it->toArray(function ($value) {
            return [$value * $value];
        });

        $this->assertEquals([0, 1, 4, 9, 16, 25], $arr);
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
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testMin(IteratorClass $it, $min, $max, callable $predicate = null)
    {
        $this->assertEquals($min, $it->min(ComparatorFactory::intComparator(), $predicate));
    }

    /**
     * @param IteratorClass $it
     * @param number $min
     * @param number $max
     * @param callable $predicate
     *
     * @dataProvider numberDataProvider
     */
    public function testMax(IteratorClass $it, $min, $max, callable $predicate = null)
    {
        $this->assertEquals($max, $it->max(ComparatorFactory::intComparator(), $predicate));
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
            [$emptyIterator, null, null, 0, function ($item) { return strpos($item, 'x') !== false; }],
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
            [$emptyIterator, null, null],
            [$func, -2, 7],
            [$func, 1, 7, function ($item) { return $item > 0; }],
        ];
    }
}
