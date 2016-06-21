<?php

use DataTypes\Exceptions\HostnameInvalidArgumentException;

/**
 * Test HostnameInvalidArgumentException class.
 */
class HostnameInvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
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
