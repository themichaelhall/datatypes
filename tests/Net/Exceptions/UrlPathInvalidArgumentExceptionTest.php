<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net\Exceptions;

use DataTypes\Exceptions\UrlPathInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test UrlPathInvalidArgumentException class.
 */
class UrlPathInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that UrlPathInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testUrlPathInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is a UrlPathInvalidArgumentException.');

        throw new UrlPathInvalidArgumentException('This is a UrlPathInvalidArgumentException.');
    }
}
