<?php

declare(strict_types=1);

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\UrlPathLogicException;
use PHPUnit\Framework\TestCase;

/**
 * Test UrlPathLogicException class.
 */
class UrlPathLogicExceptionTest extends TestCase
{
    /**
     * Test that UrlPathLogicException is subclass of LogicException.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage This is a UrlPathLogicException.
     */
    public function testUrlPathLogicExceptionIsLogicException()
    {
        throw new UrlPathLogicException('This is a UrlPathLogicException.');
    }
}
