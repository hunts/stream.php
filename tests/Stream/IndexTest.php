<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Stream;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for global functions stream() and number_stream()
 */
class IndexTest extends TestCase
{
    public function testStream()
    {
        $this->assertInstanceOf(Stream::class, stream([]));
    }

    public function testNumberStream()
    {
        $this->assertInstanceOf(NumberStream::class, number_stream([]));
    }
}
