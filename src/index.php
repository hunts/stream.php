<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

use Stream\NumberStream;
use Stream\Stream;

if (!function_exists('stream')) {
    /**
     * Define stream function in Global.
     *
     * @param Iterator|IteratorAggregate|array $source
     * @return Stream returns a streaming object.
     */
    function stream($source): Stream
    {
        return Stream::from($source);
    }
}

if (!function_exists('number_stream')) {
    /**
     * Define stream function in Global.
     *
     * @param Iterator|IteratorAggregate|number[] $source
     * @return NumberStream the new NumberStream object.
     */
    function number_stream($source): NumberStream
    {
        return NumberStream::from($source);
    }
}
