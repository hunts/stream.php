<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Hunts Chen <hunts.c@gmail.com>
 */

namespace Stream;

/**
 * Test cases for class: Stream\Stream
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $it
     *
     * @dataProvider dataProvider
     */
    public function testSkip($it)
    {
        $this->assertEquals(4, stream($it)->skip(1)->count());
    }

    /**
     * @param $it
     *
     * @dataProvider dataProvider
     */
    public function testTake($it)
    {
        $this->assertEquals(2, stream($it)->limit(2)->count());
    }

    /**
     * @param $it
     *
     * @dataProvider dataProvider
     */
    public function testFilter($it)
    {
        $this->assertEquals(3, stream($it)->filter(function($item) { return strpos($item, 'x') !== false; })->count());
    }

    /**
     * @param $it
     *
     * @dataProvider dataProvider
     */
    public function testMap($it)
    {
        $new_it = stream($it)->map(function($value) { return $value . '.php'; });
        $this->assertEquals(
            5,
            $new_it->filter(
                function($item) {
                    return strpos($item, 'php') !== false;
                }
            )->count());
    }

    public function testSort()
    {
        $stream = stream(array(5, 2, -2, 0, 100))->sort();
        $this->assertEquals(-2, $stream->first());
        $this->assertEquals(100, $stream->last());
    }

    public function testSortByDescending()
    {
        $stream = stream(array(5, 2, -2, 0, 100))->sortByDescending();
        $this->assertEquals(100, $stream->first());
        $this->assertEquals(-2, $stream->last());
    }

    public function testSortCustom()
    {
        $stream = stream(array(5, 2, -2, 0, 100))->sort(
            ComparatorFactory::create(function($first, $second) {
                return $first < $second ? 1 : -1; // reverse order
            })
        );

        $reversed = $stream->toArray();
        $descended = $stream->sortByDescending()->toArray();

        $this->assertEquals($reversed, $descended);
    }

    public function testMultiSort()
    {
        $stream = stream(
            array(
                array(1, 3),
                array(2, 2),
                array(3, 1),
                array(4, 2),
                array(5, 2)
            )
        )->sort(ComparatorFactory::create(function($first, $second) {
            return $first[1] - $second[1];
        }))->sort(ComparatorFactory::create(function($first, $second) {
            return $first[0] - $second[0];
        }));

        $this->assertEquals(array(3, 1), $stream->first());
        $this->assertEquals(array(1, 3), $stream->last());

        $stream->rewind();
        $stream->next();
        $this->assertEquals(array(2, 2), $stream->current());

        $stream->next();
        $this->assertEquals(array(4, 2), $stream->current());

        $stream->next();
        $this->assertEquals(array(5, 2), $stream->current());
    }

    public function dataProvider()
    {
        $it = new IteratorClass(5);
        $it[0] = '0';
        $it[1] = null;
        $it[2] = 'x';
        $it[3] = 'xyz';
        $it[4] = 'kkx';

        return array(array($it));
    }
}

?>