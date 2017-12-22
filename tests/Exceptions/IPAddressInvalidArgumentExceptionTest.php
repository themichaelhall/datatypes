<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\IPAddressInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test IPAddressInvalidArgumentException class.
 */
class IPAddressInvalidArgumentExceptionTest extends TestCase
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
