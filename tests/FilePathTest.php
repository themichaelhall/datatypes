<?php

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\FilePath;

/**
 * Test FilePath class.
 */
class FilePathTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS, FilePath::parse($DS)->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz.html', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz.html')->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS . 'baz.html', FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz.html')->__toString());
    }

    /**
     * Test getDirectoryParts method.
     */
    public function testGetDirectoryParts()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame([], FilePath::parse($DS)->getDirectoryParts());
        $this->assertSame(['foo'], FilePath::parse($DS . 'foo' . $DS)->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz.html')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], FilePath::parse($DS . $DS . 'foo' . $DS . 'bar' . $DS . 'baz.html')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz.html')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], FilePath::parse('foo' . $DS . 'bar' . $DS . $DS . 'baz.html')->getDirectoryParts());
    }

    /**
     * Test getFilename method.
     */
    public function testGetFilename()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertNull(FilePath::parse($DS)->getFilename());
        $this->assertSame('foo.html', FilePath::parse('foo.html')->getFilename());
        $this->assertSame('baz', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz')->getFilename());
    }

    /**
     * Test isAbsolute method.
     */
    public function testIsAbsolute()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertFalse(FilePath::parse('')->isAbsolute());
        $this->assertTrue(FilePath::parse($DS)->isAbsolute());
        $this->assertFalse(FilePath::parse('foo')->isAbsolute());
        $this->assertTrue(FilePath::parse($DS . 'foo')->isAbsolute());
        $this->assertFalse(FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz')->isAbsolute());
        $this->assertTrue(FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz')->isAbsolute());
    }

    /**
     * Test isRelative method.
     */
    public function testIsRelative()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertTrue(FilePath::parse('')->isRelative());
        $this->assertFalse(FilePath::parse($DS)->isRelative());
        $this->assertTrue(FilePath::parse('foo')->isRelative());
        $this->assertFalse(FilePath::parse($DS . 'foo')->isRelative());
        $this->assertTrue(FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz')->isRelative());
        $this->assertFalse(FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz')->isRelative());
    }

    /**
     * Test with current directory parts.
     */
    public function testWithCurrentDirectoryParts()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . '.' . $DS . 'baz' . $DS)->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS . 'bar' . $DS . '.' . $DS)->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('.' . $DS . 'foo' . $DS . 'bar' . $DS)->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS . '.' . $DS . 'file')->__toString());
    }

    /**
     * Test with parent directory parts.
     */
    public function testWithParentDirectoryParts()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS . 'foo' . $DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . '..' . $DS . 'baz' . $DS)->__toString());
        $this->assertSame($DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS)->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz' . $DS . '..')->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz' . $DS . '..' . $DS . 'file')->__toString());
    }

    /**
     * Test with parent directory parts that results in a directory above base directory.
     */
    public function testWithParentDirectoryPartsAboveBaseDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('foo' . $DS . 'bar' . $DS . '..' . $DS . '..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file.html');

        $this->assertSame('..' . $DS . '..' . $DS . 'baz' . $DS . 'file.html', $filePath->__toString());
        $this->assertTrue($filePath->isRelative());
        $this->assertSame(['..', '..', 'baz'], $filePath->getDirectoryParts());
    }

    /**
     * Test that file path with invalid character in directory is invalid.
     */
    public function testPathWithInvalidCharacterInDirectoryIsInvalid()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . "\0" . 'bar' . $DS);
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame('File path "' . $DS . 'foo' . $DS . "\0" . 'bar' . $DS . '" is invalid: Part of directory "' . "\0" . 'bar" contains invalid character "' . "\0" . '".', $exceptionMessage);
    }

    /**
     * Test that file path with invalid character in filename is invalid.
     */
    public function testPathWithInvalidCharacterInFilenameIsInvalid()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . "\0" . 'bar');
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame('File path "' . $DS . 'foo' . $DS . "\0" . 'bar" is invalid: Filename "' . "\0" . 'bar" contains invalid character "' . "\0" . '".', $exceptionMessage);
    }

    /**
     * Test that absolute file path above root level is invalid.
     */
    public function testAbsoluteUrlPathAboveRootLevelIsInvalid()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . '..' . $DS . '..' . $DS);
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame('File path "' . $DS . 'foo' . $DS . '..' . $DS . '..' . $DS . '" is invalid: Absolute path is above root level.', $exceptionMessage);
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame('', FilePath::tryParse('')->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS . 'baz.html', FilePath::tryParse('foo' . $DS . 'bar' . $DS . 'baz.html')->__toString());
        $this->assertNull(FilePath::tryParse($DS . 'foo' . "\0" . 'bar' . $DS));
        $this->assertNull(FilePath::tryParse($DS . 'foo' . $DS . '..' . $DS . '..' . $DS));
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertTrue(FilePath::isValid(''));
        $this->assertTrue(FilePath::isValid('foo' . $DS . 'bar' . $DS . 'baz.html'));
        $this->assertFalse(FilePath::isValid($DS . 'foo' . "\0" . 'bar' . $DS));
        $this->assertFalse(FilePath::isValid($DS . 'foo' . $DS . '..' . $DS . '..' . $DS));
    }
}
