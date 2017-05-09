<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\UrlPathInvalidArgumentException;

/**
 * Test UrlPathInvalidArgumentException class.
 */
class UrlPathInvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that UrlPathInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a UrlPathInvalidArgumentException.
     */
    public function testUrlPathInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new UrlPathInvalidArgumentException('This is a UrlPathInvalidArgumentException.');
    }
}
