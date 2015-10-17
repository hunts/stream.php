<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Hunts Chen <hunts.c@gmail.com>
 */

namespace Stream;

use Stream\Traits\IteratorExtension;


/**
 * An example class that implements the \Iterator interface.
 */
class IteratorClass extends \SplFixedArray implements \Iterator
{
    use IteratorExtension;
}

?>