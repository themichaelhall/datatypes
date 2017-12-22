<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\HostInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test HostInvalidArgumentException class.
 */
class HostInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that HostInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a HostInvalidArgumentException.
     */
    public function testHostnameInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new HostInvalidArgumentException('This is a HostInvalidArgumentException.');
    }
}
