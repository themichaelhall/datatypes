<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\UrlInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test UrlInvalidArgumentException class.
 */
class UrlInvalidArgumentExceptionTest extends TestCase
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
