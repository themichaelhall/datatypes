<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net\Exceptions;

use DataTypes\Exceptions\HostInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test HostInvalidArgumentException class.
 */
class HostInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that HostInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testHostnameInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is a HostInvalidArgumentException.');

        throw new HostInvalidArgumentException('This is a HostInvalidArgumentException.');
    }
}
