<?php

use DataTypes\Exceptions\IPAddressInvalidArgumentException;

/**
 * Test IPAddressInvalidArgumentException class.
 */
class IPAddressInvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that IPAddressInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is an IPAddressInvalidArgumentException.
     */
    public function testIPAddressInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new IPAddressInvalidArgumentException('This is an IPAddressInvalidArgumentException.');
    }
}
