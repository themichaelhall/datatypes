<?php

declare(strict_types=1);

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\SchemeInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test SchemeInvalidArgumentException class.
 */
class SchemeInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that SchemeInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a SchemeInvalidArgumentException.
     */
    public function testSchemeInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new SchemeInvalidArgumentException('This is a SchemeInvalidArgumentException.');
    }
}
