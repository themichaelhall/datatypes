<?php

namespace DataTypes\Tests;

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\Exceptions\FilePathLogicException;
use DataTypes\FilePath;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/Helpers/Fakes/FakePhpUname.php';

/**
 * Test FilePath class.
 */
class FilePathTest extends TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS, FilePath::parse($DS)->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz.html', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz.html')->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS . 'baz.html', FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz.html')->__toString());
    }

    /**
     * Test getDirectoryParts method.
     */
    public function testGetDirectoryParts()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame([], FilePath::parse($DS)->getDirectoryParts());
        self::assertSame(['foo'], FilePath::parse($DS . 'foo' . $DS)->getDirectoryParts());
        self::assertSame(['foo', 'bar'], FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz.html')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], FilePath::parse($DS . $DS . 'foo' . $DS . 'bar' . $DS . 'baz.html')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz.html')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], FilePath::parse('foo' . $DS . 'bar' . $DS . $DS . 'baz.html')->getDirectoryParts());
    }

    /**
     * Test getFilename method.
     */
    public function testGetFilename()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertNull(FilePath::parse($DS)->getFilename());
        self::assertSame('foo.html', FilePath::parse('foo.html')->getFilename());
        self::assertSame('baz', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz')->getFilename());
    }

    /**
     * Test isAbsolute method.
     */
    public function testIsAbsolute()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertFalse(FilePath::parse('')->isAbsolute());
        self::assertTrue(FilePath::parse($DS)->isAbsolute());
        self::assertFalse(FilePath::parse('foo')->isAbsolute());
        self::assertTrue(FilePath::parse($DS . 'foo')->isAbsolute());
        self::assertFalse(FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz')->isAbsolute());
        self::assertTrue(FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz')->isAbsolute());
    }

    /**
     * Test isRelative method.
     */
    public function testIsRelative()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertTrue(FilePath::parse('')->isRelative());
        self::assertFalse(FilePath::parse($DS)->isRelative());
        self::assertTrue(FilePath::parse('foo')->isRelative());
        self::assertFalse(FilePath::parse($DS . 'foo')->isRelative());
        self::assertTrue(FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz')->isRelative());
        self::assertFalse(FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . 'baz')->isRelative());
    }

    /**
     * Test with current directory parts.
     */
    public function testWithCurrentDirectoryParts()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . '.' . $DS . 'baz' . $DS)->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS . 'bar' . $DS . '.' . $DS)->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('.' . $DS . 'foo' . $DS . 'bar' . $DS)->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS . '.' . $DS . 'file')->__toString());
    }

    /**
     * Test with parent directory parts.
     */
    public function testWithParentDirectoryParts()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS . 'foo' . $DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . '..' . $DS . 'baz' . $DS)->__toString());
        self::assertSame($DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS)->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz' . $DS . '..')->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS . 'baz' . $DS . '..' . $DS . 'file')->__toString());
    }

    /**
     * Test with parent directory parts that results in a directory above base directory.
     */
    public function testWithParentDirectoryPartsAboveBaseDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('foo' . $DS . 'bar' . $DS . '..' . $DS . '..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file.html');

        self::assertSame('..' . $DS . '..' . $DS . 'baz' . $DS . 'file.html', $filePath->__toString());
        self::assertTrue($filePath->isRelative());
        self::assertSame(['..', '..', 'baz'], $filePath->getDirectoryParts());
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

        self::assertSame('File path "' . $DS . 'foo' . $DS . "\0" . 'bar' . $DS . '" is invalid: Part of directory "' . "\0" . 'bar" contains invalid character "' . "\0" . '".', $exceptionMessage);
    }

    /**
     * Test that path with invalid windows character in directory is invalid in windows.
     */
    public function testPathWithInvalidWindowsCharacterInDirectoryIsInvalidInWindows()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . '<bar' . $DS);
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "' . $DS . 'foo' . $DS . '<bar' . $DS . '" is invalid: Part of directory "<bar" contains invalid character "<".', $exceptionMessage);
    }

    /**
     * Test that path with invalid windows character in directory is valid on other operations systems.
     */
    public function testPathWithInvalidWindowsCharacterInDirectoryIsValidInOther()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS . 'foo' . $DS . '<bar' . $DS, FilePath::parse($DS . 'foo' . $DS . '<bar' . $DS)->__toString());
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

        self::assertSame('File path "' . $DS . 'foo' . $DS . "\0" . 'bar" is invalid: Filename "' . "\0" . 'bar" contains invalid character "' . "\0" . '".', $exceptionMessage);
    }

    /**
     * Test that path with invalid windows character in filename is invalid in windows.
     */
    public function testPathWithInvalidWindowsCharacterInFilenameIsInvalidInWindows()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . '|bar');
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "' . $DS . 'foo' . $DS . '|bar" is invalid: Filename "|bar" contains invalid character "|".', $exceptionMessage);
    }

    /**
     * Test that path with invalid windows character in filename is valid on other operations systems.
     */
    public function testPathWithInvalidWindowsCharacterInFilenameIsValidInOther()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS . 'foo' . $DS . '|bar' . $DS, FilePath::parse($DS . 'foo' . $DS . '|bar' . $DS)->__toString());
    }

    /**
     * Test that absolute file path above root level is invalid.
     */
    public function testAbsoluteFilePathAboveRootLevelIsInvalid()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . '..' . $DS . '..' . $DS);
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "' . $DS . 'foo' . $DS . '..' . $DS . '..' . $DS . '" is invalid: Absolute path is above root level.', $exceptionMessage);
    }

    /**
     * Test parse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $filePath parameter is not a string.
     */
    public function testParseWithInvalidArgumentType()
    {
        FilePath::parse(1.0);
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('', FilePath::tryParse('')->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS . 'baz.html', FilePath::tryParse('foo' . $DS . 'bar' . $DS . 'baz.html')->__toString());
        self::assertNull(FilePath::tryParse($DS . 'foo' . "\0" . 'bar' . $DS));
        self::assertNull(FilePath::tryParse($DS . 'foo' . $DS . '..' . $DS . '..' . $DS));
    }

    /**
     * Test tryParse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $filePath parameter is not a string.
     */
    public function testTryParseWithInvalidArgumentType()
    {
        FilePath::tryParse(false);
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertTrue(FilePath::isValid(''));
        self::assertTrue(FilePath::isValid('foo' . $DS . 'bar' . $DS . 'baz.html'));
        self::assertFalse(FilePath::isValid($DS . 'foo' . "\0" . 'bar' . $DS));
        self::assertFalse(FilePath::isValid($DS . 'foo' . $DS . '..' . $DS . '..' . $DS));
    }

    /**
     * Test isValid method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $filePath parameter is not a string.
     */
    public function testIsValidWithInvalidArgumentType()
    {
        FilePath::isValid(1234);
    }

    /**
     * Test isFile method.
     */
    public function testIsFile()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertFalse(FilePath::parse('')->isFile());
        self::assertFalse(FilePath::parse($DS)->isFile());
        self::assertTrue(FilePath::parse('foo')->isFile());
        self::assertTrue(FilePath::parse($DS . 'foo')->isFile());
        self::assertFalse(FilePath::parse('foo' . $DS)->isFile());
        self::assertFalse(FilePath::parse($DS . 'foo' . $DS)->isFile());
        self::assertTrue(FilePath::parse('foo' . $DS . 'bar')->isFile());
        self::assertTrue(FilePath::parse($DS . 'foo' . $DS . 'bar')->isFile());
    }

    /**
     * Test isDirectory method.
     */
    public function testIsDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertTrue(FilePath::parse('')->isDirectory());
        self::assertTrue(FilePath::parse($DS)->isDirectory());
        self::assertFalse(FilePath::parse('foo')->isDirectory());
        self::assertFalse(FilePath::parse($DS . 'foo')->isDirectory());
        self::assertTrue(FilePath::parse('foo' . $DS)->isDirectory());
        self::assertTrue(FilePath::parse($DS . 'foo' . $DS)->isDirectory());
        self::assertFalse(FilePath::parse('foo' . $DS . 'bar')->isDirectory());
        self::assertFalse(FilePath::parse($DS . 'foo' . $DS . 'bar')->isDirectory());
    }

    /**
     * Test getDirectory method.
     */
    public function testGetDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('', FilePath::parse('')->getDirectory()->__toString());
        self::assertSame($DS, FilePath::parse($DS)->getDirectory()->__toString());
        self::assertSame('', FilePath::parse('foo')->getDirectory()->__toString());
        self::assertSame($DS, FilePath::parse($DS . 'foo')->getDirectory()->__toString());
        self::assertSame('foo' . $DS, FilePath::parse('foo' . $DS)->getDirectory()->__toString());
        self::assertSame($DS . 'foo' . $DS, FilePath::parse($DS . 'foo' . $DS)->getDirectory()->__toString());
        self::assertSame('foo' . $DS, FilePath::parse('foo' . $DS . 'bar')->getDirectory()->__toString());
        self::assertSame($DS . 'foo' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar')->getDirectory()->__toString());
        self::assertSame('..' . $DS, FilePath::parse('..' . $DS . 'foo')->getDirectory()->__toString());
        self::assertSame('..' . $DS . 'foo' . $DS, FilePath::parse('..' . $DS . 'foo' . $DS)->getDirectory()->__toString());
        self::assertSame('..' . $DS . 'foo' . $DS, FilePath::parse('..' . $DS . 'foo' . $DS . 'bar')->getDirectory()->__toString());
    }

    /**
     * Test getDepth method.
     */
    public function testGetDepth()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame(0, FilePath::parse('')->getDepth());
        self::assertSame(0, FilePath::parse($DS)->getDepth());
        self::assertSame(0, FilePath::parse('foo')->getDepth());
        self::assertSame(0, FilePath::parse($DS . 'foo')->getDepth());
        self::assertSame(1, FilePath::parse('foo' . $DS)->getDepth());
        self::assertSame(1, FilePath::parse($DS . 'foo' . $DS)->getDepth());
        self::assertSame(1, FilePath::parse('foo' . $DS . 'bar')->getDepth());
        self::assertSame(1, FilePath::parse($DS . 'foo' . $DS . 'bar')->getDepth());
        self::assertSame(2, FilePath::parse('foo' . $DS . 'bar' . $DS)->getDepth());
        self::assertSame(2, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->getDepth());
        self::assertSame(-1, FilePath::parse('..' . $DS)->getDepth());
        self::assertSame(-1, FilePath::parse('..' . $DS . 'foo')->getDepth());
        self::assertSame(-2, FilePath::parse('..' . $DS . '..' . $DS . 'foo')->getDepth());
        self::assertSame(-1, FilePath::parse('..' . $DS . '..' . $DS . 'foo' . $DS)->getDepth());
    }

    /**
     * Test toRelative method.
     */
    public function testToRelative()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('', FilePath::parse('')->toRelative()->__toString());
        self::assertSame('', FilePath::parse($DS)->toRelative()->__toString());
        self::assertSame('foo', FilePath::parse('foo')->toRelative()->__toString());
        self::assertSame('foo', FilePath::parse($DS . 'foo')->toRelative()->__toString());
        self::assertSame('foo' . $DS . 'bar', FilePath::parse('foo' . $DS . 'bar')->toRelative()->__toString());
        self::assertSame('foo' . $DS . 'bar', FilePath::parse($DS . 'foo' . $DS . 'bar')->toRelative()->__toString());
    }

    /**
     * Test toRelative method with FilePath with drive.
     */
    public function testToRelativeWithDrive()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('', FilePath::parse('C:' . $DS)->toRelative()->__toString());
        self::assertSame('foo' . $DS . 'bar', FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->toRelative()->__toString());
    }

    /**
     * Test toAbsolute method.
     */
    public function testToAbsolute()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS, FilePath::parse('')->toAbsolute()->__toString());
        self::assertSame($DS, FilePath::parse($DS)->toAbsolute()->__toString());
        self::assertSame($DS . 'foo', FilePath::parse('foo')->toAbsolute()->__toString());
        self::assertSame($DS . 'foo', FilePath::parse($DS . 'foo')->toAbsolute()->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar', FilePath::parse('foo' . $DS . 'bar')->toAbsolute()->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar', FilePath::parse($DS . 'foo' . $DS . 'bar')->toAbsolute()->__toString());
    }

    /**
     * Test that attempting to make an absolute path for a file path above root is invalid.
     */
    public function testToAbsoluteForFilePathAboveRootIsInvalid()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse('..' . $DS)->toAbsolute();
        } catch (FilePathLogicException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "..' . $DS . '" can not be made absolute: Relative path is above base level.', $exceptionMessage);
    }

    /**
     * Test withFilePath method.
     */
    public function testWithFilePath()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame($DS . 'bar', FilePath::parse($DS . 'foo')->withFilePath(FilePath::parse($DS . 'bar'))->__toString());
        self::assertSame($DS . 'bar', FilePath::parse('foo')->withFilePath(FilePath::parse($DS . 'bar'))->__toString());
        self::assertSame($DS . 'bar' . $DS . 'baz', FilePath::parse($DS . 'foo')->withFilePath(FilePath::parse($DS . 'bar' . $DS . 'baz'))->__toString());
        self::assertSame($DS . 'bar' . $DS . 'baz', FilePath::parse('foo')->withFilePath(FilePath::parse($DS . 'bar' . $DS . 'baz'))->__toString());
        self::assertSame('', FilePath::parse('')->withFilePath(FilePath::parse(''))->__toString());
        self::assertSame($DS, FilePath::parse('')->withFilePath(FilePath::parse($DS))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar', FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar'))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'baz', FilePath::parse($DS . 'foo' . $DS . 'bar')->withFilePath(FilePath::parse('baz'))->__toString());
        self::assertSame('foo' . $DS . 'bar', FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar'))->__toString());
        self::assertSame('foo' . $DS . 'baz', FilePath::parse('foo' . $DS . 'bar')->withFilePath(FilePath::parse('baz'))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS, FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS, FilePath::parse($DS . 'foo' . $DS . 'baz')->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS . 'baz')->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz', FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz'))->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS . 'baz', FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz'))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz' . $DS))->__toString());
        self::assertSame('foo' . $DS . 'bar' . $DS . 'baz' . $DS, FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz' . $DS))->__toString());
        self::assertSame($DS . 'foo' . $DS . 'baz' . $DS . 'file', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame('foo' . $DS . 'baz' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame('..' . $DS . 'foo' . $DS . 'baz' . $DS . 'file', FilePath::parse('..' . $DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame($DS . 'baz' . $DS . 'file', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame('baz' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame('..' . $DS . 'baz' . $DS . 'file', FilePath::parse('..' . $DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame('..' . $DS . 'baz' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        self::assertSame('..' . $DS . '..' . $DS . 'baz' . $DS . 'file', FilePath::parse('..' . $DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
    }

    /**
     * Test that combining an absolute file path with a file path that results in a path above root level is invalid.
     */
    public function testAbsoluteFilePathWithFilePathAboveRootLevelIsInvalid()
    {
        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file'));
        } catch (FilePathLogicException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "' . $DS . 'foo' . $DS . 'bar' . $DS . '" can not be combined with file path "..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file": Absolute path is above root level.', $exceptionMessage);
    }

    /**
     * Test parse a file path with volume in windows.
     */
    public function testParseWithVolumeInWindows()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('C:' . $DS . 'path' . $DS . 'file');

        self::assertSame('C', $filePath->getDrive());
        self::assertSame(['path'], $filePath->getDirectoryParts());
        self::assertSame('file', $filePath->getFilename());
        self::assertSame('C:' . $DS . 'path' . $DS . 'file', $filePath->__toString());
        self::assertTrue($filePath->isAbsolute());
    }

    /**
     * Test parse a file path with lower case volume in windows.
     */
    public function testParseWithLowerCaseVolumeInWindows()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('c:' . $DS . 'path' . $DS . 'file');

        self::assertSame('C', $filePath->getDrive());
        self::assertSame(['path'], $filePath->getDirectoryParts());
        self::assertSame('file', $filePath->getFilename());
        self::assertSame('C:' . $DS . 'path' . $DS . 'file', $filePath->__toString());
        self::assertTrue($filePath->isAbsolute());
    }

    /**
     * Test parse a file path with volume in other operating systems.
     */
    public function testParseWithVolumeInOther()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('C:' . $DS . 'path' . $DS . 'file');

        self::assertNull($filePath->getDrive());
        self::assertSame(['C:', 'path'], $filePath->getDirectoryParts());
        self::assertSame('file', $filePath->getFilename());
        self::assertSame('C:' . $DS . 'path' . $DS . 'file', $filePath->__toString());
        self::assertFalse($filePath->isAbsolute());
    }

    /**
     * Test parse a file path with lower case volume in other operating systems.
     */
    public function testParseWithLowerCaseVolumeInOther()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('c:' . $DS . 'path' . $DS . 'file');

        self::assertNull($filePath->getDrive());
        self::assertSame(['c:', 'path'], $filePath->getDirectoryParts());
        self::assertSame('file', $filePath->getFilename());
        self::assertSame('c:' . $DS . 'path' . $DS . 'file', $filePath->__toString());
        self::assertFalse($filePath->isAbsolute());
    }

    /**
     * Test parse a file path with drive and relative path is invalid.
     */
    public function testParseWithDriveAndRelativePathIsInvalid()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse('C:foo' . $DS . 'bar');
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "C:foo' . $DS . 'bar" is invalid: Path can not contain drive "C" and non-absolute path "foo' . $DS . 'bar".', $exceptionMessage);
    }

    /**
     * Test parse a file path with invalid drive is invalid.
     */
    public function testParseWithInvalidDriveIsInvalid()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse('2:' . $DS . 'foo' . $DS . 'bar');
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        self::assertSame('File path "2:' . $DS . 'foo' . $DS . 'bar" is invalid: Drive "2" is invalid.', $exceptionMessage);
    }

    /**
     * Test that forward slash is always a valid directory separator.
     */
    public function testForwardSlashIsAlwaysDirectorySeparator()
    {
        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('/foo/bar/baz');

        self::assertSame(['foo', 'bar'], $filePath->getDirectoryParts());
        self::assertSame('baz', $filePath->getFilename());
        self::assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz', $filePath->__toString());
    }

    /**
     * Test withFilePath method for paths that contains drives.
     */
    public function testWithFilePathWithDriveInFilePath()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('C:' . $DS . 'baz', FilePath::parse('foo' . $DS . 'bar')->withFilePath(FilePath::parse('C:' . $DS . 'baz'))->__toString());
        self::assertSame('C:' . $DS . 'baz', FilePath::parse($DS . 'foo' . $DS . 'bar')->withFilePath(FilePath::parse('C:' . $DS . 'baz'))->__toString());
        self::assertSame('C:' . $DS . 'foo' . $DS . 'baz', FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->withFilePath(FilePath::parse('baz'))->__toString());
        self::assertSame('C:' . $DS . 'baz', FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->withFilePath(FilePath::parse($DS . 'baz'))->__toString());
        self::assertSame('D:' . $DS . 'baz', FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->withFilePath(FilePath::parse('D:' . $DS . 'baz'))->__toString());
    }

    /**
     * Test hasParentDirectory method.
     */
    public function testHasParentDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertTrue(FilePath::parse('')->hasParentDirectory());
        self::assertFalse(FilePath::parse($DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('foo')->hasParentDirectory());
        self::assertFalse(FilePath::parse($DS . 'foo')->hasParentDirectory());
        self::assertTrue(FilePath::parse('foo' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse($DS . 'foo' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('foo' . $DS . 'bar')->hasParentDirectory());
        self::assertTrue(FilePath::parse($DS . 'foo' . $DS . 'bar')->hasParentDirectory());
        self::assertTrue(FilePath::parse('foo' . $DS . 'bar' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('..' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('..' . $DS . 'foo')->hasParentDirectory());
        self::assertTrue(FilePath::parse('..' . $DS . '..' . $DS . 'foo')->hasParentDirectory());
        self::assertTrue(FilePath::parse('..' . $DS . '..' . $DS . 'foo' . $DS)->hasParentDirectory());
    }

    /**
     * Test hasParentDirectory method with path containing a drive in windows.
     */
    public function testHasParentDirectoryWithDriveInWindows()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        self::assertFalse(FilePath::parse('C:' . $DS)->hasParentDirectory());
        self::assertFalse(FilePath::parse('C:' . $DS . 'foo')->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS . 'baz')->hasParentDirectory());
    }

    /**
     * Test hasParentDirectory method with path containing a drive in other operating systems.
     */
    public function testHasParentDirectoryWithDriveInOther()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        self::assertTrue(FilePath::parse('C:' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo')->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS)->hasParentDirectory());
        self::assertTrue(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS . 'baz')->hasParentDirectory());
    }

    /**
     * Test getParentDirectory method.
     */
    public function testGetParentDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('..' . $DS, FilePath::parse('')->getParentDirectory()->__toString());
        self::assertNull(FilePath::parse($DS)->getParentDirectory());
        self::assertSame('..' . $DS, FilePath::parse('foo')->getParentDirectory()->__toString());
        self::assertNull(FilePath::parse($DS . 'foo')->getParentDirectory());
        self::assertSame('', FilePath::parse('foo' . $DS)->getParentDirectory()->__toString());
        self::assertSame($DS, FilePath::parse($DS . 'foo' . $DS)->getParentDirectory()->__toString());
        self::assertSame('', FilePath::parse('foo' . $DS . 'bar')->getParentDirectory()->__toString());
        self::assertSame($DS, FilePath::parse($DS . 'foo' . $DS . 'bar')->getParentDirectory()->__toString());
        self::assertSame('foo' . $DS, FilePath::parse('foo' . $DS . 'bar' . $DS)->getParentDirectory()->__toString());
        self::assertSame($DS . 'foo' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->getParentDirectory()->__toString());
        self::assertSame('..' . $DS . '..' . $DS, FilePath::parse('..' . $DS)->getParentDirectory()->__toString());
        self::assertSame('..' . $DS . '..' . $DS, FilePath::parse('..' . $DS . 'foo')->getParentDirectory()->__toString());
        self::assertSame('..' . $DS . '..' . $DS . '..' . $DS, FilePath::parse('..' . $DS . '..' . $DS . 'foo')->getParentDirectory()->__toString());
        self::assertSame('..' . $DS . '..' . $DS, FilePath::parse('..' . $DS . '..' . $DS . 'foo' . $DS)->getParentDirectory()->__toString());
    }

    /**
     * Test getParentDirectory method with path containing a drive in windows.
     */
    public function testGetParentDirectoryWithDriveInWindows()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        self::assertNull(FilePath::parse('C:' . $DS)->getParentDirectory());
        self::assertNull(FilePath::parse('C:' . $DS . 'foo')->getParentDirectory());
        self::assertSame('C:' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS)->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS . 'foo' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS)->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS . 'foo' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS . 'baz')->getParentDirectory()->__toString());
    }

    /**
     * Test getParentDirectory method with path containing a drive in other operating systems.
     */
    public function testGetParentDirectoryWithDriveInOther()
    {
        \FakePhpUname::enable();
        \FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        self::assertSame('', FilePath::parse('C:' . $DS)->getParentDirectory()->__toString());
        self::assertSame('', FilePath::parse('C:' . $DS . 'foo')->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS)->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS . 'foo' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS)->getParentDirectory()->__toString());
        self::assertSame('C:' . $DS . 'foo' . $DS, FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar' . $DS . 'baz')->getParentDirectory()->__toString());
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        $DS = DIRECTORY_SEPARATOR;

        self::assertTrue(FilePath::parse('')->equals(FilePath::parse('.' . $DS)));
        self::assertFalse(FilePath::parse('')->equals(FilePath::parse($DS)));
        self::assertTrue(FilePath::parse('.' . $DS . 'foo')->equals(FilePath::parse('.' . $DS . 'foo')));
        self::assertFalse(FilePath::parse($DS . 'foo')->equals(FilePath::parse('.' . $DS . 'foo')));
        self::assertFalse(FilePath::parse('.' . $DS . 'foo')->equals(FilePath::parse('.' . $DS . 'bar')));
        self::assertTrue(FilePath::parse('..' . $DS . 'foo')->equals(FilePath::parse('.' . $DS . '..' . $DS . 'foo')));
        self::assertFalse(FilePath::parse('C:' . $DS . 'foo' . $DS . 'bar')->equals(FilePath::parse('D:' . $DS . 'foo' . $DS . 'bar')));
    }

    /**
     * Tear down.
     */
    public function tearDown()
    {
        \FakePhpUname::disable();
    }
}
