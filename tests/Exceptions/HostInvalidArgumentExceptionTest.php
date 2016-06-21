<?php

use DataTypes\Exceptions\HostInvalidArgumentException;

/**
 * Test HostInvalidArgumentException class.
 */
class HostInvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
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
