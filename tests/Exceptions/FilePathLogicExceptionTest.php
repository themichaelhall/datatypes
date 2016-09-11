<?php

use DataTypes\Exceptions\FilePathLogicException;

/**
 * Test FilePathLogicException class.
 */
class FilePathLogicExceptionTest extends PHPUnit_Framework_TestCase
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
