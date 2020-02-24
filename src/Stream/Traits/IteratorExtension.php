<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream\Traits;

use Hunts\Stream\Comparator;
use Hunts\Stream\Stream;

/**
 * Useful methods for class that is going to implements Iterator interface.
 */
trait IteratorExtension
{
    /**
     * Return the current element.
     *
     * @return mixed Can return any type.
     */
    abstract public function current();

    /**
     * Return the key of the current element.
     *
     * @return mixed Scalar on success, or null on failure
     */
    abstract public function key();

    /**
     * Move forward to next element.
     *
     * @return void Any returned value is ignored.
     */
    abstract public function next();

    /**
     * Checks if current position is valid.
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    abstract public function valid();

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void Any returned value is ignored.
     */
    abstract public function rewind();

    /**
     * Return stream of current iterator object. If this object is stream, just return itself.
     *
     * @return Stream
     */
    public function streamify(): Stream
    {
        if ($this instanceof Stream) {
            return $this;
        }

        return Stream::from($this);
    }

    /**
     *
     *
     * @param callable|null $mapper [optional] mapper function.
     *  the mapper can return array with 2 elements.
     *  First one to be used as $value, second one as $key.
     *
     * @return array
     */
    public function toArray(callable $mapper = null): array
    {
        if ($mapper === null) {
            return iterator_to_array($this);
        }

        $result = [];

        $this->each(function ($item) use ($result, $mapper) {
            list($value, $key) = $mapper($item);
            if ($key === null) {
                $result[] = $value;
            } else {
                $result[$key] = $value;
            }
        });

        return $result;
    }

    /**
     *
     *
     * @param callable|null $predicate
     *
     * @return bool
     */
    public function any(callable $predicate = null): bool
    {
        $this->rewind();

        if ($predicate === null) {
            return $this->valid();
        }

        while ($this->valid()) {
            if ($predicate($this->current())) {
                return true;
            }
            $this->next();
        }

        return false;
    }

    /**
     *
     *
     * @param callable|null $predicate
     *
     * @return bool
     */
    public function all(callable $predicate = null): bool
    {
        if ($predicate === null) {
            return true;
        }

        $this->rewind();

        while ($this->valid()) {
            if (!$predicate($this->current())) {
                return false;
            }
            $this->next();
        }

        return true;
    }

    /**
     * Returns the first element of matched items, or null if the stream
     * is empty.
     *
     * @param callable|null $predicate [optional] predicate expression.
     *
     * @return mixed Can return any type on success or null on nothing found.
     */
    public function first(callable $predicate = null)
    {
        $this->rewind();

        while ($this->valid()) {
            if ($predicate === null || $predicate($this->current())) {
                return $this->current();
            }
            $this->next();
        }

        return null;
    }

    /**
     * Returns the last element of matched items, or null if the stream
     * is empty.
     *
     * @param callable $predicate [optional] predicate expression.
     *
     * @return mixed Can return any type on success or null on nothing found.
     */
    public function last(callable $predicate = null)
    {
        $result = null;

        if (!$this->valid()) {
            $this->rewind();
        }

        while ($this->valid()) {
            if ($predicate === null || $predicate($this->current())) {
                $result = $this->current();
            }
            $this->next();
        }

        return $result;
    }

    /**
     * Return the total number of matched elements.
     *
     * @param callable $predicate [optional] predicate expression.
     *
     * @return int
     */
    public function count(callable $predicate = null): int
    {
        if ($predicate === null) {
            return iterator_count($this);
        }

        return $this->reduce(function ($total, $item) {
            return $total + 1;
        }, 0, $predicate);
    }

    /**
     *
     * @param callable $callback
     * @param callable $predicate [optional] predicate expression.
     *
     * @return void
     */
    public function each(callable $callback, callable $predicate = null)
    {
        foreach ($this as $item) {
            if (isset($predicate) && !$predicate($item)) {
                continue;
            }

            $callback($item);
        }
    }

    /**
     *
     *
     * @param Comparator $comparator
     * @param callable $predicate [optional] predicate expression.
     * @return mixed
     */
    public function max(Comparator $comparator, callable $predicate = null)
    {
        return $this->reduce(function ($a, $b) use ($comparator) {
            if ($comparator->compare($a, $b) >= 0) {
                return $a;
            }
            return $b;
        }, null, $predicate);
    }

    /**
     *
     *
     * @param Comparator $comparator
     * @param callable $predicate [optional] predicate expression.
     *
     * @return mixed
     */
    public function min(Comparator $comparator, callable $predicate = null)
    {
        return $this->reduce(function ($a, $b) use ($comparator) {
            if ($comparator->compare($a, $b) <= 0) {
                return $a;
            }
            return $b;
        }, null, $predicate);
    }

    /**
     * Performs a reduction on the elements of this stream,
     * using the provided identity value and an associative
     * accumulation function, and returns the reduced value.
     *
     * @param callable $accumulation
     *  An associative, non-interfering, stateless function
     *  for combining two values.
     * @param mixed $identity [optional]
     *  The identity value for the accumulating function.
     * @param callable $predicate [optional]
     *  A predicate expression.
     *
     * @return mixed the result of the reduction
     */
    public function reduce(callable $accumulation, $identity = null, callable $predicate = null)
    {
        $result = $identity;

        $this->each(function ($item) use (&$result, $accumulation) {

            if (!isset($result)) {
                $result = $item;
                return;
            }

            $result = $accumulation($result, $item);

        }, $predicate);

        return $result;
    }
}
