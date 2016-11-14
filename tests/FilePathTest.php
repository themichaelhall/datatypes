<?php

use DataTypes\Exceptions\FilePathInvalidArgumentException;
use DataTypes\Exceptions\FilePathLogicException;
use DataTypes\FilePath;

require_once __DIR__ . '/Helpers/Fakes/FakePhpUname.php';

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
     * Test that path with invalid windows character in directory is invalid in windows.
     */
    public function testPathWithInvalidWindowsCharacterInDirectoryIsInvalidInWindows()
    {
        FakePhpUname::enable();
        FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . '<bar' . $DS);
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame('File path "' . $DS . 'foo' . $DS . '<bar' . $DS . '" is invalid: Part of directory "<bar" contains invalid character "<".', $exceptionMessage);
    }

    /**
     * Test that path with invalid windows character in directory is valid on other operations systems.
     */
    public function testPathWithInvalidWindowsCharacterInDirectoryIsValidInOther()
    {
        FakePhpUname::enable();
        FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS . 'foo' . $DS . '<bar' . $DS, FilePath::parse($DS . 'foo' . $DS . '<bar' . $DS)->__toString());
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
     * Test that path with invalid windows character in filename is invalid in windows.
     */
    public function testPathWithInvalidWindowsCharacterInFilenameIsInvalidInWindows()
    {
        FakePhpUname::enable();
        FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;
        $exceptionMessage = '';

        try {
            FilePath::parse($DS . 'foo' . $DS . '|bar');
        } catch (FilePathInvalidArgumentException $e) {
            $exceptionMessage = $e->getMessage();
        }

        $this->assertSame('File path "' . $DS . 'foo' . $DS . '|bar" is invalid: Filename "|bar" contains invalid character "|".', $exceptionMessage);
    }

    /**
     * Test that path with invalid windows character in filename is valid on other operations systems.
     */
    public function testPathWithInvalidWindowsCharacterInFilenameIsValidInOther()
    {
        FakePhpUname::enable();
        FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS . 'foo' . $DS . '|bar' . $DS, FilePath::parse($DS . 'foo' . $DS . '|bar' . $DS)->__toString());
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

    /**
     * Test isFile method.
     */
    public function testIsFile()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertFalse(FilePath::parse('')->isFile());
        $this->assertFalse(FilePath::parse($DS)->isFile());
        $this->assertTrue(FilePath::parse('foo')->isFile());
        $this->assertTrue(FilePath::parse($DS . 'foo')->isFile());
        $this->assertFalse(FilePath::parse('foo' . $DS)->isFile());
        $this->assertFalse(FilePath::parse($DS . 'foo' . $DS)->isFile());
        $this->assertTrue(FilePath::parse('foo' . $DS . 'bar')->isFile());
        $this->assertTrue(FilePath::parse($DS . 'foo' . $DS . 'bar')->isFile());
    }

    /**
     * Test isDirectory method.
     */
    public function testIsDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertTrue(FilePath::parse('')->isDirectory());
        $this->assertTrue(FilePath::parse($DS)->isDirectory());
        $this->assertFalse(FilePath::parse('foo')->isDirectory());
        $this->assertFalse(FilePath::parse($DS . 'foo')->isDirectory());
        $this->assertTrue(FilePath::parse('foo' . $DS)->isDirectory());
        $this->assertTrue(FilePath::parse($DS . 'foo' . $DS)->isDirectory());
        $this->assertFalse(FilePath::parse('foo' . $DS . 'bar')->isDirectory());
        $this->assertFalse(FilePath::parse($DS . 'foo' . $DS . 'bar')->isDirectory());
    }

    /**
     * Test getDirectory method.
     */
    public function testGetDirectory()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame('', FilePath::parse('')->getDirectory()->__toString());
        $this->assertSame($DS, FilePath::parse($DS)->getDirectory()->__toString());
        $this->assertSame('', FilePath::parse('foo')->getDirectory()->__toString());
        $this->assertSame($DS, FilePath::parse($DS . 'foo')->getDirectory()->__toString());
        $this->assertSame('foo' . $DS, FilePath::parse('foo' . $DS)->getDirectory()->__toString());
        $this->assertSame($DS . 'foo' . $DS, FilePath::parse($DS . 'foo' . $DS)->getDirectory()->__toString());
        $this->assertSame('foo' . $DS, FilePath::parse('foo' . $DS . 'bar')->getDirectory()->__toString());
        $this->assertSame($DS . 'foo' . $DS, FilePath::parse($DS . 'foo' . $DS . 'bar')->getDirectory()->__toString());
        $this->assertSame('..' . $DS, FilePath::parse('..' . $DS . 'foo')->getDirectory()->__toString());
        $this->assertSame('..' . $DS . 'foo' . $DS, FilePath::parse('..' . $DS . 'foo' . $DS)->getDirectory()->__toString());
        $this->assertSame('..' . $DS . 'foo' . $DS, FilePath::parse('..' . $DS . 'foo' . $DS . 'bar')->getDirectory()->__toString());
    }

    /**
     * Test getDepth method.
     */
    public function testGetDepth()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame(0, FilePath::parse('')->getDepth());
        $this->assertSame(0, FilePath::parse($DS)->getDepth());
        $this->assertSame(0, FilePath::parse('foo')->getDepth());
        $this->assertSame(0, FilePath::parse($DS . 'foo')->getDepth());
        $this->assertSame(1, FilePath::parse('foo' . $DS)->getDepth());
        $this->assertSame(1, FilePath::parse($DS . 'foo' . $DS)->getDepth());
        $this->assertSame(1, FilePath::parse('foo' . $DS . 'bar')->getDepth());
        $this->assertSame(1, FilePath::parse($DS . 'foo' . $DS . 'bar')->getDepth());
        $this->assertSame(2, FilePath::parse('foo' . $DS . 'bar' . $DS)->getDepth());
        $this->assertSame(2, FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->getDepth());
        $this->assertSame(-1, FilePath::parse('..' . $DS)->getDepth());
        $this->assertSame(-1, FilePath::parse('..' . $DS . 'foo')->getDepth());
        $this->assertSame(-2, FilePath::parse('..' . $DS . '..' . $DS . 'foo')->getDepth());
        $this->assertSame(-1, FilePath::parse('..' . $DS . '..' . $DS . 'foo' . $DS)->getDepth());
    }

    /**
     * Test toRelative method.
     */
    public function testToRelative()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame('', FilePath::parse('')->toRelative()->__toString());
        $this->assertSame('', FilePath::parse($DS)->toRelative()->__toString());
        $this->assertSame('foo', FilePath::parse('foo')->toRelative()->__toString());
        $this->assertSame('foo', FilePath::parse($DS . 'foo')->toRelative()->__toString());
        $this->assertSame('foo' . $DS . 'bar', FilePath::parse('foo' . $DS . 'bar')->toRelative()->__toString());
        $this->assertSame('foo' . $DS . 'bar', FilePath::parse($DS . 'foo' . $DS . 'bar')->toRelative()->__toString());
    }

    /**
     * Test toAbsolute method.
     */
    public function testToAbsolute()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS, FilePath::parse('')->toAbsolute()->__toString());
        $this->assertSame($DS, FilePath::parse($DS)->toAbsolute()->__toString());
        $this->assertSame($DS . 'foo', FilePath::parse('foo')->toAbsolute()->__toString());
        $this->assertSame($DS . 'foo', FilePath::parse($DS . 'foo')->toAbsolute()->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar', FilePath::parse('foo' . $DS . 'bar')->toAbsolute()->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar', FilePath::parse($DS . 'foo' . $DS . 'bar')->toAbsolute()->__toString());
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

        $this->assertSame('File path "..' . $DS . '" can not be made absolute: Relative path is above base level.', $exceptionMessage);
    }

    /**
     * Test withFilePath method.
     */
    public function testWithFilePath()
    {
        $DS = DIRECTORY_SEPARATOR;

        $this->assertSame($DS . 'bar', FilePath::parse($DS . 'foo')->withFilePath(FilePath::parse($DS . 'bar'))->__toString());
        $this->assertSame($DS . 'bar', FilePath::parse('foo')->withFilePath(FilePath::parse($DS . 'bar'))->__toString());
        $this->assertSame($DS . 'bar' . $DS . 'baz', FilePath::parse($DS . 'foo')->withFilePath(FilePath::parse($DS . 'bar' . $DS . 'baz'))->__toString());
        $this->assertSame($DS . 'bar' . $DS . 'baz', FilePath::parse('foo')->withFilePath(FilePath::parse($DS . 'bar' . $DS . 'baz'))->__toString());
        $this->assertSame('', FilePath::parse('')->withFilePath(FilePath::parse(''))->__toString());
        $this->assertSame($DS, FilePath::parse('')->withFilePath(FilePath::parse($DS))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar', FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar'))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'baz', FilePath::parse($DS . 'foo' . $DS . 'bar')->withFilePath(FilePath::parse('baz'))->__toString());
        $this->assertSame('foo' . $DS . 'bar', FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar'))->__toString());
        $this->assertSame('foo' . $DS . 'baz', FilePath::parse('foo' . $DS . 'bar')->withFilePath(FilePath::parse('baz'))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS, FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS, FilePath::parse($DS . 'foo' . $DS . 'baz')->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS, FilePath::parse('foo' . $DS . 'baz')->withFilePath(FilePath::parse('bar' . $DS))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz', FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz'))->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS . 'baz', FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz'))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz' . $DS, FilePath::parse($DS . 'foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz' . $DS))->__toString());
        $this->assertSame('foo' . $DS . 'bar' . $DS . 'baz' . $DS, FilePath::parse('foo' . $DS)->withFilePath(FilePath::parse('bar' . $DS . 'baz' . $DS))->__toString());
        $this->assertSame($DS . 'foo' . $DS . 'baz' . $DS . 'file', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame('foo' . $DS . 'baz' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame('..' . $DS . 'foo' . $DS . 'baz' . $DS . 'file', FilePath::parse('..' . $DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame($DS . 'baz' . $DS . 'file', FilePath::parse($DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame('baz' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame('..' . $DS . 'baz' . $DS . 'file', FilePath::parse('..' . $DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame('..' . $DS . 'baz' . $DS . 'file', FilePath::parse('foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
        $this->assertSame('..' . $DS . '..' . $DS . 'baz' . $DS . 'file', FilePath::parse('..' . $DS . 'foo' . $DS . 'bar' . $DS)->withFilePath(FilePath::parse('..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file'))->__toString());
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

        $this->assertSame('File path "' . $DS . 'foo' . $DS . 'bar' . $DS . '" can not be combined with file path "..' . $DS . '..' . $DS . '..' . $DS . 'baz' . $DS . 'file": Absolute path is above root level.', $exceptionMessage);
    }

    /**
     * Test parse a file path with volume in windows.
     */
    public function testParseWithVolumeInWindows()
    {
        FakePhpUname::enable();
        FakePhpUname::setOsName('Windows NT');

        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('C:' . $DS . 'path' . $DS . 'file');

        $this->assertSame('C', $filePath->getDrive());
        $this->assertSame(['path'], $filePath->getDirectoryParts());
        $this->assertSame('file', $filePath->getFilename());
        $this->assertSame('C:' . $DS . 'path' . $DS . 'file', $filePath->__toString());
        $this->assertTrue($filePath->isAbsolute());
    }

    /**
     * Test parse a file path with volume in other operating systems.
     */
    public function testParseWithVolumeInOther()
    {
        FakePhpUname::enable();
        FakePhpUname::setOsName('Other');

        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('C:' . $DS . 'path' . $DS . 'file');

        $this->assertNull($filePath->getDrive());
        $this->assertSame(['C:', 'path'], $filePath->getDirectoryParts());
        $this->assertSame('file', $filePath->getFilename());
        $this->assertSame('C:' . $DS . 'path' . $DS . 'file', $filePath->__toString());
        $this->assertFalse($filePath->isAbsolute());
    }

    /**
     * Test that forward slash is always a valid directory separator.
     */
    public function testForwardSlashIsAlwaysDirectorySeparator()
    {
        $DS = DIRECTORY_SEPARATOR;

        $filePath = FilePath::parse('/foo/bar/baz');

        $this->assertSame(['foo', 'bar'], $filePath->getDirectoryParts());
        $this->assertSame('baz', $filePath->getFilename());
        $this->assertSame($DS . 'foo' . $DS . 'bar' . $DS . 'baz', $filePath->__toString());
    }

    /**
     * Tear down.
     */
    public function tearDown()
    {
        FakePhpUname::disable();
    }
}
