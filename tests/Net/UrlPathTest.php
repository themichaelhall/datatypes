<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net;

use DataTypes\Net\Exceptions\UrlPathInvalidArgumentException;
use DataTypes\Net\Exceptions\UrlPathLogicException;
use DataTypes\Net\UrlPath;
use PHPUnit\Framework\TestCase;

/**
 * Test UrlPath class.
 */
class UrlPathTest extends TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        self::assertSame('/', UrlPath::parse('/')->__toString());
        self::assertSame('/foo/bar/', UrlPath::parse('/foo/bar/')->__toString());
        self::assertSame('/foo/bar/baz.html', UrlPath::parse('/foo/bar/baz.html')->__toString());
        self::assertSame('foo/bar/baz.html', UrlPath::parse('foo/bar/baz.html')->__toString());
    }

    /**
     * Test getDirectoryParts method.
     */
    public function testGetDirectoryParts()
    {
        self::assertSame([], UrlPath::parse('/')->getDirectoryParts());
        self::assertSame(['foo'], UrlPath::parse('/foo/')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], UrlPath::parse('/foo/bar/baz.html')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], UrlPath::parse('//foo/bar/baz.html')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], UrlPath::parse('foo/bar/baz.html')->getDirectoryParts());
        self::assertSame(['foo', 'bar'], UrlPath::parse('foo/bar//baz.html')->getDirectoryParts());
    }

    /**
     * Test getFilename method.
     */
    public function testGetFilename()
    {
        self::assertNull(UrlPath::parse('/')->getFilename());
        self::assertSame('foo.html', UrlPath::parse('foo.html')->getFilename());
        self::assertSame('baz', UrlPath::parse('/foo/bar/baz')->getFilename());
    }

    /**
     * Test isAbsolute method.
     */
    public function testIsAbsolute()
    {
        self::assertFalse(UrlPath::parse('')->isAbsolute());
        self::assertTrue(UrlPath::parse('/')->isAbsolute());
        self::assertFalse(UrlPath::parse('foo')->isAbsolute());
        self::assertTrue(UrlPath::parse('/foo')->isAbsolute());
        self::assertFalse(UrlPath::parse('foo/bar/baz')->isAbsolute());
        self::assertTrue(UrlPath::parse('/foo/bar/baz')->isAbsolute());
    }

    /**
     * Test isRelative method.
     */
    public function testIsRelative()
    {
        self::assertTrue(UrlPath::parse('')->isRelative());
        self::assertFalse(UrlPath::parse('/')->isRelative());
        self::assertTrue(UrlPath::parse('foo')->isRelative());
        self::assertFalse(UrlPath::parse('/foo')->isRelative());
        self::assertTrue(UrlPath::parse('foo/bar/baz')->isRelative());
        self::assertFalse(UrlPath::parse('/foo/bar/baz')->isRelative());
    }

    /**
     * Test with current directory parts.
     */
    public function testWithCurrentDirectoryParts()
    {
        self::assertSame('/foo/bar/baz/', UrlPath::parse('/foo/bar/./baz/')->__toString());
        self::assertSame('foo/bar/', UrlPath::parse('foo/bar/./')->__toString());
        self::assertSame('foo/bar/', UrlPath::parse('./foo/bar/')->__toString());
        self::assertSame('foo/bar/file', UrlPath::parse('foo/bar/./file')->__toString());
    }

    /**
     * Test with parent directory parts.
     */
    public function testWithParentDirectoryParts()
    {
        self::assertSame('/foo/baz/', UrlPath::parse('/foo/bar/../baz/')->__toString());
        self::assertSame('/baz/', UrlPath::parse('/foo/bar/../../baz/')->__toString());
        self::assertSame('foo/bar/', UrlPath::parse('foo/bar/baz/..')->__toString());
        self::assertSame('foo/bar/file', UrlPath::parse('foo/bar/baz/../file')->__toString());
    }

    /**
     * Test with parent directory parts that results in a directory above base directory.
     */
    public function testWithParentDirectoryPartsAboveBaseDirectory()
    {
        $urlPath = UrlPath::parse('foo/bar/../../../../baz/file.html');

        self::assertSame('../../baz/file.html', $urlPath->__toString());
        self::assertTrue($urlPath->isRelative());
        self::assertSame(['..', '..', 'baz'], $urlPath->getDirectoryParts());
    }

    /**
     * Test that url path with invalid character in directory is invalid.
     */
    public function testPathWithInvalidCharacterInDirectoryIsInvalid()
    {
        self::expectException(UrlPathInvalidArgumentException::class);
        self::expectExceptionMessage('Url path "/foo/{bar}/" is invalid: Part of directory "{bar}" contains invalid character "{".');

        UrlPath::parse('/foo/{bar}/');
    }

    /**
     * Test that url path with invalid character in filename is invalid.
     */
    public function testPathWithInvalidCharacterInFilenameIsInvalid()
    {
        self::expectException(UrlPathInvalidArgumentException::class);
        self::expectExceptionMessage('Url path "/foo/bar?html" is invalid: Filename "bar?html" contains invalid character "?".');

        UrlPath::parse('/foo/bar?html');
    }

    /**
     * Test that url path is correctly decoded and encoded.
     */
    public function testUrlPathIsDecodedAndEncoded()
    {
        $urlPath = UrlPath::parse('/path%3f!/file%3f!');

        self::assertSame('/path%3F%21/file%3F%21', $urlPath->__toString());
        self::assertSame(['path?!'], $urlPath->getDirectoryParts());
        self::assertSame('file?!', $urlPath->getFilename());
    }

    /**
     * Test that absolute url path above root level is invalid.
     */
    public function testAbsoluteUrlPathAboveRootLevelIsInvalid()
    {
        self::expectException(UrlPathInvalidArgumentException::class);
        self::expectExceptionMessage('Url path "/foo/../../" is invalid: Absolute path is above root level.');

        UrlPath::parse('/foo/../../');
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertSame('', UrlPath::tryParse('')->__toString());
        self::assertSame('foo/bar/baz.html', UrlPath::tryParse('foo/bar/baz.html')->__toString());
        self::assertNull(UrlPath::tryParse('/foo/{bar}/'));
        self::assertNull(UrlPath::tryParse('/foo/../../'));
        self::assertSame('/path%3F%21/file%3F%21', UrlPath::tryParse('/path%3f!/file%3f!')->__toString());
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertTrue(UrlPath::isValid(''));
        self::assertTrue(UrlPath::isValid('foo/bar/baz.html'));
        self::assertFalse(UrlPath::isValid('/foo/{bar}/'));
        self::assertFalse(UrlPath::isValid('/foo/../../'));
    }

    /**
     * Test isFile method.
     */
    public function testIsFile()
    {
        self::assertFalse(UrlPath::parse('')->isFile());
        self::assertFalse(UrlPath::parse('/')->isFile());
        self::assertTrue(UrlPath::parse('foo')->isFile());
        self::assertTrue(UrlPath::parse('/foo')->isFile());
        self::assertFalse(UrlPath::parse('foo/')->isFile());
        self::assertFalse(UrlPath::parse('/foo/')->isFile());
        self::assertTrue(UrlPath::parse('foo/bar')->isFile());
        self::assertTrue(UrlPath::parse('/foo/bar')->isFile());
    }

    /**
     * Test isDirectory method.
     */
    public function testIsDirectory()
    {
        self::assertTrue(UrlPath::parse('')->isDirectory());
        self::assertTrue(UrlPath::parse('/')->isDirectory());
        self::assertFalse(UrlPath::parse('foo')->isDirectory());
        self::assertFalse(UrlPath::parse('/foo')->isDirectory());
        self::assertTrue(UrlPath::parse('foo/')->isDirectory());
        self::assertTrue(UrlPath::parse('/foo/')->isDirectory());
        self::assertFalse(UrlPath::parse('foo/bar')->isDirectory());
        self::assertFalse(UrlPath::parse('/foo/bar')->isDirectory());
    }

    /**
     * Test getDirectory method.
     */
    public function testGetDirectory()
    {
        self::assertSame('', UrlPath::parse('')->getDirectory()->__toString());
        self::assertSame('/', UrlPath::parse('/')->getDirectory()->__toString());
        self::assertSame('', UrlPath::parse('foo')->getDirectory()->__toString());
        self::assertSame('/', UrlPath::parse('/foo')->getDirectory()->__toString());
        self::assertSame('foo/', UrlPath::parse('foo/')->getDirectory()->__toString());
        self::assertSame('/foo/', UrlPath::parse('/foo/')->getDirectory()->__toString());
        self::assertSame('foo/', UrlPath::parse('foo/bar')->getDirectory()->__toString());
        self::assertSame('/foo/', UrlPath::parse('/foo/bar')->getDirectory()->__toString());
        self::assertSame('../', UrlPath::parse('../foo')->getDirectory()->__toString());
        self::assertSame('../foo/', UrlPath::parse('../foo/')->getDirectory()->__toString());
        self::assertSame('../foo/', UrlPath::parse('../foo/bar')->getDirectory()->__toString());
    }

    /**
     * Test getDepth method.
     */
    public function testGetDepth()
    {
        self::assertSame(0, UrlPath::parse('')->getDepth());
        self::assertSame(0, UrlPath::parse('/')->getDepth());
        self::assertSame(0, UrlPath::parse('foo')->getDepth());
        self::assertSame(0, UrlPath::parse('/foo')->getDepth());
        self::assertSame(1, UrlPath::parse('foo/')->getDepth());
        self::assertSame(1, UrlPath::parse('/foo/')->getDepth());
        self::assertSame(1, UrlPath::parse('foo/bar')->getDepth());
        self::assertSame(1, UrlPath::parse('/foo/bar')->getDepth());
        self::assertSame(2, UrlPath::parse('foo/bar/')->getDepth());
        self::assertSame(2, UrlPath::parse('/foo/bar/')->getDepth());
        self::assertSame(-1, UrlPath::parse('../')->getDepth());
        self::assertSame(-1, UrlPath::parse('../foo')->getDepth());
        self::assertSame(-2, UrlPath::parse('../../foo')->getDepth());
        self::assertSame(-1, UrlPath::parse('../../foo/')->getDepth());
    }

    /**
     * Test toRelative method.
     */
    public function testToRelative()
    {
        self::assertSame('', UrlPath::parse('')->toRelative()->__toString());
        self::assertSame('', UrlPath::parse('/')->toRelative()->__toString());
        self::assertSame('foo', UrlPath::parse('foo')->toRelative()->__toString());
        self::assertSame('foo', UrlPath::parse('/foo')->toRelative()->__toString());
        self::assertSame('foo/bar', UrlPath::parse('foo/bar')->toRelative()->__toString());
        self::assertSame('foo/bar', UrlPath::parse('/foo/bar')->toRelative()->__toString());
    }

    /**
     * Test toAbsolute method.
     */
    public function testToAbsolute()
    {
        self::assertSame('/', UrlPath::parse('')->toAbsolute()->__toString());
        self::assertSame('/', UrlPath::parse('/')->toAbsolute()->__toString());
        self::assertSame('/foo', UrlPath::parse('foo')->toAbsolute()->__toString());
        self::assertSame('/foo', UrlPath::parse('/foo')->toAbsolute()->__toString());
        self::assertSame('/foo/bar', UrlPath::parse('foo/bar')->toAbsolute()->__toString());
        self::assertSame('/foo/bar', UrlPath::parse('/foo/bar')->toAbsolute()->__toString());
    }

    /**
     * Test that attempting to make an absolute path for a url path above root is invalid.
     */
    public function testToAbsoluteForUrlPathAboveRootIsInvalid()
    {
        self::expectException(UrlPathLogicException::class);
        self::expectExceptionMessage('Url path "../" can not be made absolute: Relative path is above base level.');

        UrlPath::parse('../')->toAbsolute();
    }

    /**
     * Test withUrlPath method.
     */
    public function testWithUrlPath()
    {
        self::assertSame('/bar', UrlPath::parse('/foo')->withUrlPath(UrlPath::parse('/bar'))->__toString());
        self::assertSame('/bar', UrlPath::parse('foo')->withUrlPath(UrlPath::parse('/bar'))->__toString());
        self::assertSame('/bar/baz', UrlPath::parse('/foo')->withUrlPath(UrlPath::parse('/bar/baz'))->__toString());
        self::assertSame('/bar/baz', UrlPath::parse('foo')->withUrlPath(UrlPath::parse('/bar/baz'))->__toString());
        self::assertSame('', UrlPath::parse('')->withUrlPath(UrlPath::parse(''))->__toString());
        self::assertSame('/', UrlPath::parse('')->withUrlPath(UrlPath::parse('/'))->__toString());
        self::assertSame('/foo/bar', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar'))->__toString());
        self::assertSame('/foo/baz', UrlPath::parse('/foo/bar')->withUrlPath(UrlPath::parse('baz'))->__toString());
        self::assertSame('foo/bar', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar'))->__toString());
        self::assertSame('foo/baz', UrlPath::parse('foo/bar')->withUrlPath(UrlPath::parse('baz'))->__toString());
        self::assertSame('/foo/bar/', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        self::assertSame('foo/bar/', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        self::assertSame('/foo/bar/', UrlPath::parse('/foo/baz')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        self::assertSame('foo/bar/', UrlPath::parse('foo/baz')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        self::assertSame('/foo/bar/baz', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar/baz'))->__toString());
        self::assertSame('foo/bar/baz', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar/baz'))->__toString());
        self::assertSame('/foo/bar/baz/', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar/baz/'))->__toString());
        self::assertSame('foo/bar/baz/', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar/baz/'))->__toString());
        self::assertSame('/foo/baz/file', UrlPath::parse('/foo/bar/')->withUrlPath(UrlPath::parse('../baz/file'))->__toString());
        self::assertSame('foo/baz/file', UrlPath::parse('foo/bar/')->withUrlPath(UrlPath::parse('../baz/file'))->__toString());
        self::assertSame('../foo/baz/file', UrlPath::parse('../foo/bar/')->withUrlPath(UrlPath::parse('../baz/file'))->__toString());
        self::assertSame('/baz/file', UrlPath::parse('/foo/bar/')->withUrlPath(UrlPath::parse('../../baz/file'))->__toString());
        self::assertSame('baz/file', UrlPath::parse('foo/bar/')->withUrlPath(UrlPath::parse('../../baz/file'))->__toString());
        self::assertSame('../baz/file', UrlPath::parse('../foo/bar/')->withUrlPath(UrlPath::parse('../../baz/file'))->__toString());
        self::assertSame('../baz/file', UrlPath::parse('foo/bar/')->withUrlPath(UrlPath::parse('../../../baz/file'))->__toString());
        self::assertSame('../../baz/file', UrlPath::parse('../foo/bar/')->withUrlPath(UrlPath::parse('../../../baz/file'))->__toString());
    }

    /**
     * Test that combining an absolute url path with an url path that results in a path above root level is invalid.
     */
    public function testAbsoluteUrlPathWithUrlPathAboveRootLevelIsInvalid()
    {
        self::expectException(UrlPathLogicException::class);
        self::expectExceptionMessage('Url path "/foo/bar/" can not be combined with url path "../../../baz/file": Absolute path is above root level.');

        UrlPath::parse('/foo/bar/')->withUrlPath(UrlPath::parse('../../../baz/file'));
    }

    /**
     * Test hasParentDirectory method.
     */
    public function testHasParentDirectory()
    {
        self::assertTrue(UrlPath::parse('')->hasParentDirectory());
        self::assertFalse(UrlPath::parse('/')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('foo')->hasParentDirectory());
        self::assertFalse(UrlPath::parse('/foo')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('foo/')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('/foo/')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('foo/bar')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('/foo/bar')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('foo/bar/')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('/foo/bar/')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('../')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('../foo')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('../../foo')->hasParentDirectory());
        self::assertTrue(UrlPath::parse('../../foo/')->hasParentDirectory());
    }

    /**
     * Test getParentDirectory method.
     */
    public function testGetParentDirectory()
    {
        self::assertSame('../', UrlPath::parse('')->getParentDirectory()->__toString());
        self::assertNull(UrlPath::parse('/')->getParentDirectory());
        self::assertSame('../', UrlPath::parse('foo')->getParentDirectory()->__toString());
        self::assertNull(UrlPath::parse('/foo')->getParentDirectory());
        self::assertSame('', UrlPath::parse('foo/')->getParentDirectory()->__toString());
        self::assertSame('/', UrlPath::parse('/foo/')->getParentDirectory()->__toString());
        self::assertSame('', UrlPath::parse('foo/bar')->getParentDirectory()->__toString());
        self::assertSame('/', UrlPath::parse('/foo/bar')->getParentDirectory()->__toString());
        self::assertSame('foo/', UrlPath::parse('foo/bar/')->getParentDirectory()->__toString());
        self::assertSame('/foo/', UrlPath::parse('/foo/bar/')->getParentDirectory()->__toString());
        self::assertSame('../../', UrlPath::parse('../')->getParentDirectory()->__toString());
        self::assertSame('../../', UrlPath::parse('../foo')->getParentDirectory()->__toString());
        self::assertSame('../../../', UrlPath::parse('../../foo')->getParentDirectory()->__toString());
        self::assertSame('../../', UrlPath::parse('../../foo/')->getParentDirectory()->__toString());
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(UrlPath::parse('')->equals(UrlPath::parse('./')));
        self::assertFalse(UrlPath::parse('')->equals(UrlPath::parse('/')));
        self::assertTrue(UrlPath::parse('./foo')->equals(UrlPath::parse('./foo')));
        self::assertFalse(UrlPath::parse('/foo')->equals(UrlPath::parse('./foo')));
        self::assertFalse(UrlPath::parse('./foo')->equals(UrlPath::parse('./bar')));
        self::assertTrue(UrlPath::parse('../foo')->equals(UrlPath::parse('./../foo')));
    }

    /**
     * Test withFilename method.
     */
    public function testWithFilename()
    {
        self::assertSame('', UrlPath::parse('')->withFilename('')->__toString());
        self::assertSame('foo', UrlPath::parse('')->withFilename('foo')->__toString());
        self::assertSame('foo', UrlPath::parse('bar')->withFilename('foo')->__toString());
        self::assertSame('foo/', UrlPath::parse('foo/')->withFilename('')->__toString());
        self::assertSame('foo/baz', UrlPath::parse('foo/')->withFilename('baz')->__toString());
        self::assertSame('foo/', UrlPath::parse('foo/bar')->withFilename('')->__toString());
        self::assertSame('foo/baz', UrlPath::parse('foo/bar')->withFilename('baz')->__toString());
    }

    /**
     * Test withFilename method with a directory name.
     */
    public function testWithFilenameWithDirectoryName()
    {
        self::expectException(UrlPathInvalidArgumentException::class);
        self::expectExceptionMessage('Filename "bar/baz" contains invalid character "/".');

        UrlPath::parse('foo')->withFilename('bar/baz');
    }

    /**
     * Test withFilename method with a invalid character.
     */
    public function testWithFilenameWithInvalidCharacter()
    {
        self::expectException(UrlPathInvalidArgumentException::class);
        self::expectExceptionMessage('Filename "bar?baz" contains invalid character "?".');

        UrlPath::parse('foo')->withFilename('bar?baz');
    }

    /**
     * Test parseAsDirectory method.
     */
    public function testParseAsDirectory()
    {
        self::assertSame('', UrlPath::parseAsDirectory('')->__toString());
        self::assertSame('foo/', UrlPath::parseAsDirectory('foo')->__toString());
        self::assertSame('foo/', UrlPath::parseAsDirectory('foo/')->__toString());
        self::assertSame('foo/bar/', UrlPath::parseAsDirectory('foo/bar')->__toString());
        self::assertSame('foo/bar/', UrlPath::parseAsDirectory('foo/bar/')->__toString());
        self::assertSame('/', UrlPath::parseAsDirectory('/')->__toString());
        self::assertSame('/foo/', UrlPath::parseAsDirectory('/foo')->__toString());
        self::assertSame('/foo/', UrlPath::parseAsDirectory('/foo/')->__toString());
        self::assertSame('/foo/bar/', UrlPath::parseAsDirectory('/foo/bar')->__toString());
        self::assertSame('/foo/bar/', UrlPath::parseAsDirectory('/foo/bar/')->__toString());
        self::assertSame('/foo/baz/', UrlPath::parseAsDirectory('/foo/bar/../baz')->__toString());
        self::assertSame('/foo/baz/', UrlPath::parseAsDirectory('/foo/bar/../baz/')->__toString());
    }

    /**
     * Test parseAsDirectory method with invalid characters in filename.
     */
    public function testParseAsDirectoryWithInvalidCharactersInFilename()
    {
        self::expectException(UrlPathInvalidArgumentException::class);
        self::expectExceptionMessage('Url path "/foo/?bar" is invalid: Filename "?bar" contains invalid character "?".');

        UrlPath::parseAsDirectory('/foo/?bar');
    }

    /**
     * Test tryParseAsDirectory method.
     */
    public function testTryParseAsDirectory()
    {
        self::assertSame('', UrlPath::tryParseAsDirectory('')->__toString());
        self::assertSame('foo/', UrlPath::tryParseAsDirectory('foo')->__toString());
        self::assertSame('foo/', UrlPath::tryParseAsDirectory('foo/')->__toString());
        self::assertSame('foo/bar/', UrlPath::tryParseAsDirectory('foo/bar')->__toString());
        self::assertSame('foo/bar/', UrlPath::tryParseAsDirectory('foo/bar/')->__toString());
        self::assertSame('/', UrlPath::tryParseAsDirectory('/')->__toString());
        self::assertSame('/foo/', UrlPath::tryParseAsDirectory('/foo')->__toString());
        self::assertSame('/foo/', UrlPath::tryParseAsDirectory('/foo/')->__toString());
        self::assertSame('/foo/bar/', UrlPath::tryParseAsDirectory('/foo/bar')->__toString());
        self::assertSame('/foo/bar/', UrlPath::tryParseAsDirectory('/foo/bar/')->__toString());
        self::assertSame('/foo/baz/', UrlPath::tryParseAsDirectory('/foo/bar/../baz')->__toString());
        self::assertSame('/foo/baz/', UrlPath::tryParseAsDirectory('/foo/bar/../baz/')->__toString());
        self::assertNull(UrlPath::tryParseAsDirectory('/foo/?bar'));
    }
}
