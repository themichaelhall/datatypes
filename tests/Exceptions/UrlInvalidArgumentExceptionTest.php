<?php

declare(strict_types=1);

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\UrlInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test UrlInvalidArgumentException class.
 */
class UrlInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that UrlInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testUrlInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is a UrlInvalidArgumentException.');

        throw new UrlInvalidArgumentException('This is a UrlInvalidArgumentException.');
    }
}
