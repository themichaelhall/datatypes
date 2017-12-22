<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test FilePathInvalidArgumentException class.
 */
class FilePathInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that FilePathInvalidArgumentException is subclass of InvalidArgumentException.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage This is a FilePathInvalidArgumentException.
     */
    public function testFilePathInvalidArgumentExceptionIsInvalidArgumentException()
    {
        throw new FilePathInvalidArgumentException('This is a FilePathInvalidArgumentException.');
    }
}
