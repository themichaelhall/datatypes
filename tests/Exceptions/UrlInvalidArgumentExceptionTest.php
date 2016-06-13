<?php

use DataTypes\Exceptions\UrlInvalidArgumentException;

/**
 * Test UrlInvalidArgumentException class.
 */
class UrlInvalidArgumentExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that UrlInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a UrlInvalidArgumentException.
     */
    public function testUrlInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new UrlInvalidArgumentException('This is a UrlInvalidArgumentException.');
    }
}
