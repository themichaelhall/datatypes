<?php

declare(strict_types=1);

namespace DataTypes\Tests\System\Exceptions;

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test FilePathInvalidArgumentException class.
 */
class FilePathInvalidArgumentExceptionTest extends TestCase
{
    /**
     * Test that FilePathInvalidArgumentException is subclass of InvalidArgumentException.
     */
    public function testFilePathInvalidArgumentExceptionIsInvalidArgumentException()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This is a FilePathInvalidArgumentException.');

        throw new FilePathInvalidArgumentException('This is a FilePathInvalidArgumentException.');
    }
}
