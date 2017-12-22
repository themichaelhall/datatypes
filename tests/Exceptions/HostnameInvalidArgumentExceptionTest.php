<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\HostnameInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test HostnameInvalidArgumentException class.
 */
class HostnameInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that HostnameInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a HostnameInvalidArgumentException.
     */
    public function testHostnameInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new HostnameInvalidArgumentException('This is a HostnameInvalidArgumentException.');
    }
}
