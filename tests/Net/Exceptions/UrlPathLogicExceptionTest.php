<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net\Exceptions;

use DataTypes\Net\Exceptions\UrlPathLogicException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Test UrlPathLogicException class.
 */
class UrlPathLogicExceptionTest extends TestCase
{
    /**
     * Test that UrlPathLogicException is subclass of LogicException.
     */
    public function testUrlPathLogicExceptionIsLogicException()
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('This is a UrlPathLogicException.');

        throw new UrlPathLogicException('This is a UrlPathLogicException.');
    }
}
