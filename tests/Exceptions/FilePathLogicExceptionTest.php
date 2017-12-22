<?php

namespace DataTypes\Tests\Exceptions;

use DataTypes\Exceptions\FilePathLogicException;
use PHPUnit\Framework\TestCase;

/**
 * Test FilePathLogicException class.
 */
class FilePathLogicExceptionTest extends TestCase
{
    /**
     * Test that FilePathLogicException is subclass of LogicException.
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage This is a FilePathLogicException.
     */
    public function testFilePathLogicExceptionIsLogicException()
    {
        throw new FilePathLogicException('This is a FilePathLogicException.');
    }
}
