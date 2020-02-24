<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream;


/**
 *
 */
interface Comparator
{
    /**
     * Flag: <i>NULL</i> is treated as zero value
     */
    public const NULL_AS_ZERO = 0b0;

    /**
     * Flag: <i>NULL</i> is treated less than zero value
     */
    public const NULL_LT_ZERO = 0b1;

    /**
     * Flag: <i>NULL</i> is treated greater then any non-null value
     */
    public const NULL_GT_ANY = 0b10;

    /**
     * @param mixed $first
     * @param mixed $second
     *
     * @return int Returns <0 if $first is less than $second;
     *  0 they are equal, >0 if $first is greater than $second
     */
    public function compare($first, $second): int;
}
