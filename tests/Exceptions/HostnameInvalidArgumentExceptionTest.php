<?php

declare(strict_types=1);

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\HostnameInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test HostnameInvalidArgumentException class.
 */
class HostnameInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that HostnameInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testHostnameInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is a HostnameInvalidArgumentException.');

        throw new HostnameInvalidArgumentException('This is a HostnameInvalidArgumentException.');
    }
}
