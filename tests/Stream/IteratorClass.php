<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream;

use Hunts\Stream\Traits\IteratorExtension;
use Iterator;
use SplFixedArray;

/**
 * An example class that implements the \Iterator interface.
 */
class IteratorClass extends SplFixedArray implements Iterator
{
    use IteratorExtension;
}
