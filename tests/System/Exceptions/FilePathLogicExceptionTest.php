<?php

declare(strict_types=1);

namespace DataTypes\Tests\System\Exceptions;

use DataTypes\Exceptions\FilePathLogicException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Test FilePathLogicException class.
 */
class FilePathLogicExceptionTest extends TestCase
{
    /**
     * Test that FilePathLogicException is subclass of LogicException.
     */
    public function testFilePathLogicExceptionIsLogicException()
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('This is a FilePathLogicException.');

        throw new FilePathLogicException('This is a FilePathLogicException.');
    }
}
