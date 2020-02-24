<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream;

use ArrayIterator;
use Countable;
use Hunts\Stream\Traits\IteratorExtension;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;

/**
 * Stream Class.
 *
 * Provide streaming API to marshal collection.
 */
class Stream implements Iterator, Countable
{
    use IteratorExtension;

    /**
     * @var Iterator
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
     * @param Iterator $it
     */
    private function __construct(Iterator $it)
    {
        $this->it = $it;
    }

    /**
     *
     *
     * @param Iterator|IteratorAggregate|array $source The source object that want to become a stream.
     *
     * @return static
     */
    final public static function from($source): self
    {
        $it = null;

        if (is_array($source)) {
            $it = new ArrayIterator($source);
        } else if ($source instanceof Iterator) {
            $it = $source;
        } else if ($source instanceof IteratorAggregate) {
            $it = $source->getIterator();
        }

        if ($it === null) {
            throw new InvalidArgumentException('invalid source type. only accepts array, Iterator, IteratorAggregate');
        }

        return new static($it);
    }

    /**
     *
     * @return bool
     */
    private function acceptIt(): bool
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

        foreach ($this->filters as $filter) {
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
     * Returns a stream consisting of the elements of this stream that
     * match the given predicate.
     *
     * @param callable $predicate a non-interfering, stateless predicate
     *  to apply to each element to determine if it should be included
     *
     * @return Stream the new stream
     */
    public function filter(callable $predicate): Stream
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
     *
     * @return Stream returns a new Stream object.
     */
    public function map(callable $mapper = null): Stream
    {
        $this->mapper = $mapper;

        return new self($this);
    }

    /**
     *
     *
     * @param callable $mapper
     *
     * @return NumberStream returns a new NumberStream object.
     */
    public function mapToNumber(callable $mapper = null): NumberStream
    {
        $this->mapper = $mapper;

        return new NumberStream($this);
    }

    /**
     * Skips certain number of items in the collection.
     *
     * @param int $skip
     * @return Stream returns self
     */
    public function skip(int $skip): Stream
    {
        $this->skip = $skip;
        return $this;
    }

    /**
     * Limits the number of returning items.
     *
     * @param int $limit
     *
     * @return Stream returns self
     */
    public function limit(int $limit): Stream
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Removes duplicate values from the collection.
     *
     * @param callable|null $hashFunc
     *  Hash function for the values. The hashes of values will be
     *  compared when this function is provided.
     *
     * @return Stream
     */
    public function distinct(callable $hashFunc = null): Stream
    {
        $generator = function () use ($hashFunc) {
            $set = [];
            foreach ($this as $key => $value) {
                if ($hashFunc !== null) {
                    $hash = $hashFunc($value);
                } else {
                    $hash = $value;
                }

                if (isset($set[$hash])) {
                    continue;
                }

                $set[$hash] = true;
                yield $key => $value;
            }
        };

        return new self($generator());
    }

    /**
     * @return bool
     */
    public function hasSortPended(): bool
    {
        return $this->sortPended;
    }

    /**
     * @param Comparator|null $comparator
     *
     * @return Stream
     */
    public function sort(Comparator $comparator = null): Stream
    {
        return $this->sortByFlag(SortOrder::ASC, $comparator);
    }

    /**
     * @param Comparator|null $comparator
     *
     * @return Stream
     */
    public function sortByDescending(Comparator $comparator = null): Stream
    {
        return $this->sortByFlag(SortOrder::DESC, $comparator);
    }

    /**
     * @param int $sortOrder
     * @param Comparator|null $comparator
     *
     * @return Stream
     */
    private function sortByFlag($sortOrder, Comparator $comparator = null): Stream
    {
        $command = new SortingCommand($sortOrder, $comparator);

        // in case of multi-sort. That is stream->sort()->sort()->...
        if (($this->it instanceof Stream) && $this->it->sortPended) {
            if ($comparator === null || end($this->it->sortingCommands)->getComparator() === null) {
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
    private function multiSort(): void
    {
        if (!$this->sortPended || empty($this->sortingCommands)) {
            return;
        }

        // set pended to false before toArray is performed to avoid infinite loop.
        $this->sortPended = false;

        $itArray = ($this->it instanceof ArrayIterator)
            ? $this->it->getArrayCopy()
            : $this->toArray();

        if (count($this->sortingCommands) === 1) {

            $command = $this->sortingCommands[0];

            if (!is_null($command->getComparator())) {
                usort($itArray, function ($first, $second) use ($command) {
                    return -$command->getSortOrder() * $command->getComparator()->compare($first, $second);
                }
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

        $this->it = new ArrayIterator($itArray);

    }

    /**
     * @param $array
     * @param SortingCommand[] $sortingCommands
     *
     * @return array
     */
    private function quickSort($array, $sortingCommands): array
    {
        if (count($array) < 2) {
            return $array;
        }

        $left = [];
        $right = [];

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
