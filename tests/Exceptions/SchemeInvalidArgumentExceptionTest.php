<?php

use DataTypes\Exceptions\SchemeInvalidArgumentException;

/**
 * Test SchemeInvalidArgumentException class.
 */
class SchemeInvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that SchemeInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a SchemeInvalidArgumentException.
     */
    public function testSchemeInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new SchemeInvalidArgumentException('This is a SchemeInvalidArgumentException.');
    }
}
