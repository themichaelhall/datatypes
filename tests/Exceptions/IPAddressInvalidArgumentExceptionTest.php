<?php

declare(strict_types=1);

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\IPAddressInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test IPAddressInvalidArgumentException class.
 */
class IPAddressInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that IPAddressInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testIPAddressInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is an IPAddressInvalidArgumentException.');

        throw new IPAddressInvalidArgumentException('This is an IPAddressInvalidArgumentException.');
    }
}
