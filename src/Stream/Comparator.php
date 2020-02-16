<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Stream;

/**
 *
 */
interface Comparator
{
    /**
     * @param mixed $first
     * @param mixed $second
     *
     * @return int Returns <0 if $first is less than $second;
     *  0 they are equal, >0 if $first is greater than $second
     */
    public function compare($first, $second): int;
}
