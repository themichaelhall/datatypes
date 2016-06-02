<?php

use DataTypes\Exceptions\HostnameInvalidArgumentException;

/**
 * Test exceptions.
 */
class ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that HostnameInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is an HostnameInvalidArgumentException.
     */
    public function testHostnameInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new HostnameInvalidArgumentException('This is an HostnameInvalidArgumentException.');
    }
}
