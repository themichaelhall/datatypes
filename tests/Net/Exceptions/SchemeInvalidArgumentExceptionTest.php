<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net\Exceptions;

use DataTypes\Net\Exceptions\SchemeInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test SchemeInvalidArgumentException class.
 */
class SchemeInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that SchemeInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testSchemeInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is a SchemeInvalidArgumentException.');

        throw new SchemeInvalidArgumentException('This is a SchemeInvalidArgumentException.');
    }
}
