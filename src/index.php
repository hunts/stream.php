<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Hunts Chen <hunts.c@gmail.com>
 */

use Stream\Stream;

if (!function_exists('stream')) {
    /**
     * Define stream function in Global.
     *
     * @param \Iterator|\IteratorAggregate|array $source
     * @return Stream returns a streaming object.
     */
    function stream($source) {
        return Stream::from($source);
    }
}