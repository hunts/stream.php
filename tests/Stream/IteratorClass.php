<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Stream;

use Iterator;
use SplFixedArray;
use Stream\Traits\IteratorExtension;

/**
 * An example class that implements the \Iterator interface.
 */
class IteratorClass extends SplFixedArray implements Iterator
{
    use IteratorExtension;
}
