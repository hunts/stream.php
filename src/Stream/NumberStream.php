<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Stream;

/**
 * A sequence of primitive number-valued elements supporting sequential
 * and aggregate operations.
 *
 * This is the number (int, float) primitive specialization of {@see Stream}.
 */
class NumberStream extends Stream
{
    /**
     * Return sum value of matched items.
     *
     * @param callable $predicate [optional] predicate expression.
     *
     * @return number Sum.
     */
    public function sum(callable $predicate = null)
    {
        return $this->reduce(function ($a, $b) {
            return $a + $b;
        }, 0, $predicate);
    }

    /**
     * Return average value of matched items.
     *
     * @param callable $predicate [optional] predicate expression.
     *
     * @return number Average.
     */
    public function average(callable $predicate = null)
    {
        $size = 0;
        $total = $this->reduce(function ($a, $b) use (&$size) {
            $size++;
            return $a + $b;
        }, 0, $predicate);

        if ($size === 0) {
            return 0;
        }

        return $total / $size;
    }
}
