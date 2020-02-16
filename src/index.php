<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

use Stream\Stream;

if (!function_exists('stream')) {
    /**
     * Define stream function in Global.
     *
     * @param Iterator|IteratorAggregate|array $source
     * @return Stream returns a streaming object.
     *
     * @throws Exception
     */
    function stream($source): Stream
    {
        return Stream::from($source);
    }
}
