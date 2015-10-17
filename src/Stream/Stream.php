<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Hunts Chen <hunts.c@gmail.com>
 */

namespace Stream;

use Stream\Traits\IteratorExtension;


/**
 * Stream Class.
 *
 * Provide streaming API to marshal collection.
 */
class Stream implements \Iterator, \Countable
{
    use IteratorExtension;

    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @var int
     */
    private $skip;

    /**
     * @var int
     */
    private $skipped = 0;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $token = 0;

    /**
     * @var callable|null
     */
    private $mapper = null;

    /**
     * @var bool
     */
    private $acceptValidated = false;

    /**
     * @var SortingCommand[]|null
     */
    private $sortingCommands = null;

    /**
     * @var bool
     */
    private $sortPended = false;

    /**
     * @param \Iterator $it
     */
    private function __construct(\Iterator $it)
    {
        $this->it = $it;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this;
    }

    /**
     *
     *
     * @param \Iterator|\IteratorAggregate|array $source The source object that want to become a stream.
     * @return Stream
     */
    final public static function from($source)
    {
        $it = null;

        if (is_array($source)) {
            $it = new \ArrayIterator($source);
        } else if ($source instanceof \Iterator) {
            $it = $source;
        } else if ($source instanceof \IteratorAggregate) {
            $it = $source->getIterator();
        }

        if ($it === null) {
            throw new \InvalidArgumentException('invalid source type. only accepts array, Iterator, IteratorAggregate');
        }

        return new self($it);
    }

    /**
     *
     * @return bool
     */
    private function acceptIt()
    {
        if ($this->sortPended) {
            $this->multiSort();
            return $this->acceptIt();
        }

        if (!$this->it->valid()) {
            return false;
        }

        // do not change number of items in collection.
        if (empty($this->filters) && !isset($this->skip) && !isset($this->limit)) {
            return true;
        }

        // already return enough items.
        if (isset($this->limit) && $this->token === $this->limit) {
            return false;
        }

        $accept = true;

        foreach($this->filters as $filter) {
            if (!$filter($this->it->current())) {
                $accept = false;
                break;
            }
        }

        if (!$accept) {
            return false;
        }

        if (isset($this->skip) && $this->skipped < $this->skip) {
            if (!$this->acceptValidated) {
                $this->skipped++;
            }
            return false;
        }

        if (!$this->acceptValidated) {
            $this->token++;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        if (!$this->acceptValidated) {
            $this->valid();
        }

        return isset($this->mapper)
            ? call_user_func($this->mapper, $this->it->current())
            : $this->it->current();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        if (!$this->acceptValidated) {
            $this->valid();
        }

        return $this->it->key();
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->it->next();
        $this->acceptValidated = false;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        if (!$this->it->valid()) {
            return false;
        }

        $accept = $this->acceptIt();
        $this->acceptValidated = true;

        if (!$accept) {
            $this->next(); // Skip the un-accepted item.
            return $this->valid();
        }


        return true;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->it->rewind();
        $this->skipped = 0;
        $this->token = 0;
        $this->acceptValidated = false;
    }

    /**
     *
     * @param callable $predicate
     * @return Stream returns self
     */
    public function filter(callable $predicate)
    {
        if (isset($predicate)) {
            $this->filters[] = $predicate;
        }
        return $this;
    }

    /**
     *
     *
     * @param callable $mapper
     * @return Stream returns a new Stream object.
     */
    public function map(callable $mapper = null)
    {
        $this->mapper = $mapper;
        return new self($this);
    }

    /**
     * @param int $skip
     * @return Stream returns self
     */
    public function skip($skip)
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * @param int $limit
     * @return Stream returns self
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param callable|null $comparator
     * @return Stream
     */
    public function distinct(callable $comparator = null)
    {
        $generator = function() use($comparator) {
            $set = [];
            foreach ($this as $key => $value) {
                if (isset($set[$value])) {
                    continue;
                }

                $set[$value] = true;
                yield $key => $value;
            }
        };

        return new self($generator());
    }

    /**
     * @return bool
     */
    public function hasSortPended()
    {
        return $this->sortPended;
    }

    /**
     * @param Comparator|null $comparator
     * @return Stream
     */
    public function sort(Comparator $comparator = null)
    {
        return $this->sortByFlag(SortOrder::ASC, $comparator);
    }

    /**
     * @param Comparator|null $comparator
     * @return Stream
     */
    public function sortByDescending(Comparator $comparator = null)
    {
        return $this->sortByFlag(SortOrder::DESC, $comparator);
    }

    /**
     * @param int $sortOrder
     * @param Comparator|null $comparator
     * @return Stream
     */
    private function sortByFlag($sortOrder, Comparator $comparator = null)
    {
        $command = new SortingCommand($sortOrder, $comparator);

        // in case of multi-sort. That is stream->sort()->sort()->...
        if (($this->it instanceof Stream) && $this->it->sortPended) {
            if (!isset($comparator) || is_null(end($this->it->sortingCommands)->getComparator())) {
                $this->it->sortingCommands[0] = $command;
            } else {
                $this->it->sortingCommands[] = $command;
            }
            return $this;
        }

        if (!isset($this->sortingCommands)) {
            $this->sortingCommands = [$command];
        } else {
            $this->sortingCommands[] = $command;
        }

        $this->sortPended = true;

        return new self($this);
    }

    /**
     * @return void
     */
    private function multiSort()
    {
        if (!$this->sortPended || empty($this->sortingCommands)) {
            return;
        }

        // set pended to false before toArray is performed to avoid infinite loop.
        $this->sortPended = false;

        $itArray = ($this->it instanceof \ArrayIterator)
            ? $this->it->getArrayCopy()
            : $this->toArray();

        if (count($this->sortingCommands) === 1) {

            $command = $this->sortingCommands[0];

            if (!is_null($command->getComparator())) {
                usort($itArray, function($first, $second) use($command) {
                    return -$command->getSortOrder() * $command->getComparator()->compare($first, $second); }
                );
            } else {
                if ($command->getSortOrder() === SortOrder::ASC) {
                    sort($itArray);
                } else {
                    rsort($itArray);
                }
            }
        } else {
            $itArray = $this->quickSort($itArray, $this->sortingCommands);
        }

        $this->it = new \ArrayIterator($itArray);

    }

    /**
     * @param $array
     * @param SortingCommand[] $sortingCommands
     * @return array
     */
    private function quickSort($array, $sortingCommands)
    {
        if (count($array) < 2) {
            return $array;
        }

        $left = array();
        $right = array();

        $pivot_key = key($array);
        $pivot = array_shift($array);

        foreach ($array as $k => $v) {
            $toLeft = false;

            foreach ($sortingCommands as $command) {
                $ret = is_null($command->getComparator())
                    ? ($v < $pivot ? -1 : 1)
                    : $command->getComparator()->compare($v, $pivot);

                if ($ret !== 0) {
                    $toLeft = ($ret < 0 && $command->getSortOrder() === SortOrder::ASC) || ($ret > 0 && $command->getSortOrder() === SortOrder::DESC);
                    break;
                }
            }

            if ($toLeft) {
                $left[$k] = $v;
            } else {
                $right[$k] = $v;
            }
        }

        return array_merge($this->quickSort($left, $sortingCommands), array($pivot_key => $pivot), $this->quickSort($right, $sortingCommands));
    }
}

?>