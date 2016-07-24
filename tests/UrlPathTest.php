<?php

use DataTypes\UrlPath;

/**
 * Test UrlPath class.
 */
class UrlPathTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('/', UrlPath::parse('/')->__toString());
        $this->assertSame('/foo/bar/', UrlPath::parse('/foo/bar/')->__toString());
        $this->assertSame('/foo/bar/baz.html', UrlPath::parse('/foo/bar/baz.html')->__toString());
        $this->assertSame('foo/bar/baz.html', UrlPath::parse('foo/bar/baz.html')->__toString());
    }

    /**
     * Test getDirectoryParts method.
     */
    public function testGetDirectoryParts()
    {
        $this->assertSame([], UrlPath::parse('/')->getDirectoryParts());
        $this->assertSame(['foo'], UrlPath::parse('/foo/')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], UrlPath::parse('/foo/bar/baz.html')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], UrlPath::parse('//foo/bar/baz.html')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], UrlPath::parse('foo/bar/baz.html')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], UrlPath::parse('foo/bar//baz.html')->getDirectoryParts());
    }

    /**
     * Test getFilename method.
     */
    public function testGetFilename()
    {
        $this->assertNull(UrlPath::parse('/')->getFilename());
        $this->assertSame('foo.html', UrlPath::parse('foo.html')->getFilename());
        $this->assertSame('baz', UrlPath::parse('/foo/bar/baz')->getFilename());
    }

    /**
     * Test isAbsolute method.
     */
    public function testIsAbsolute()
    {
        $this->assertFalse(UrlPath::parse('')->isAbsolute());
        $this->assertTrue(UrlPath::parse('/')->isAbsolute());
        $this->assertFalse(UrlPath::parse('foo')->isAbsolute());
        $this->assertTrue(UrlPath::parse('/foo')->isAbsolute());
        $this->assertFalse(UrlPath::parse('foo/bar/baz')->isAbsolute());
        $this->assertTrue(UrlPath::parse('/foo/bar/baz')->isAbsolute());
    }

    /**
     * Test isRelative method.
     */
    public function testIsRelative()
    {
        $this->assertTrue(UrlPath::parse('')->isRelative());
        $this->assertFalse(UrlPath::parse('/')->isRelative());
        $this->assertTrue(UrlPath::parse('foo')->isRelative());
        $this->assertFalse(UrlPath::parse('/foo')->isRelative());
        $this->assertTrue(UrlPath::parse('foo/bar/baz')->isRelative());
        $this->assertFalse(UrlPath::parse('/foo/bar/baz')->isRelative());
    }

    /**
     * Test with current directory parts.
     */
    public function testWithCurrentDirectoryParts()
    {
        $this->assertSame('/foo/bar/baz/', UrlPath::parse('/foo/bar/./baz/')->__toString());
        $this->assertSame('foo/bar/', UrlPath::parse('foo/bar/./')->__toString());
        $this->assertSame('foo/bar/', UrlPath::parse('./foo/bar/')->__toString());
        $this->assertSame('foo/bar/file', UrlPath::parse('foo/bar/./file')->__toString());
    }

    /**
     * Test with parent directory parts.
     */
    public function testWithParentDirectoryParts()
    {
        $this->assertSame('/foo/baz/', UrlPath::parse('/foo/bar/../baz/')->__toString());
        $this->assertSame('/baz/', UrlPath::parse('/foo/bar/../../baz/')->__toString());
        $this->assertSame('foo/bar/', UrlPath::parse('foo/bar/baz/..')->__toString());
        $this->assertSame('foo/bar/file', UrlPath::parse('foo/bar/baz/../file')->__toString());
    }

    /**
     * Test with parent directory parts that results in a directory above base directory.
     */
    public function testWithParentDirectoryPartsAboveBaseDirectory()
    {
        $urlPath = UrlPath::parse('foo/bar/../../../../baz/file.html');

        $this->assertSame('../../baz/file.html', $urlPath->__toString());
        $this->assertTrue($urlPath->isRelative());
        $this->assertSame(['..', '..', 'baz'], $urlPath->getDirectoryParts());
    }

    /**
     * Test that url path with invalid character in directory is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlPathInvalidArgumentException
     * @expectedExceptionMessage Url path "/foo/{bar}/" is invalid: Part of directory "{bar}" contains invalid character "{".
     */
    public function testPathWithInvalidCharacterInDirectoryIsInvalid()
    {
        UrlPath::parse('/foo/{bar}/');
    }

    /**
     * Test that url path with invalid character in filename is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlPathInvalidArgumentException
     * @expectedExceptionMessage Url path "/foo/bar?html" is invalid: Filename "bar?html" contains invalid character "?".
     */
    public function testPathWithInvalidCharacterInFilenameIsInvalid()
    {
        UrlPath::parse('/foo/bar?html');
    }

    /**
     * Test that url path is correctly decoded and encoded.
     */
    public function testUrlPathIsDecodedAndEncoded()
    {
        $urlPath = UrlPath::parse('/path%3f!/file%3f!');

        $this->assertSame('/path%3F%21/file%3F%21', $urlPath->__toString());
        $this->assertSame(['path?!'], $urlPath->getDirectoryParts());
        $this->assertSame('file?!', $urlPath->getFilename());
    }

    /**
     * Test that absolute url path above root level is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlPathInvalidArgumentException
     * @expectedExceptionMessage Url path "/foo/../../" is invalid: Absolute path is above root level.
     */
    public function testAbsoluteUrlPathAboveRootLevelIsInvalid()
    {
        UrlPath::parse('/foo/../../');
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertSame('', UrlPath::tryParse('')->__toString());
        $this->assertSame('foo/bar/baz.html', UrlPath::tryParse('foo/bar/baz.html')->__toString());
        $this->assertNull(UrlPath::tryParse('/foo/{bar}/'));
        $this->assertNull(UrlPath::tryParse('/foo/../../'));
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertTrue(UrlPath::isValid(''));
        $this->assertTrue(UrlPath::isValid('foo/bar/baz.html'));
        $this->assertFalse(UrlPath::isValid('/foo/{bar}/'));
        $this->assertFalse(UrlPath::isValid('/foo/../../'));
    }

    /**
     * Test isFile method.
     */
    public function testIsFile()
    {
        $this->assertFalse(UrlPath::parse('')->isFile());
        $this->assertFalse(UrlPath::parse('/')->isFile());
        $this->assertTrue(UrlPath::parse('foo')->isFile());
        $this->assertTrue(UrlPath::parse('/foo')->isFile());
        $this->assertFalse(UrlPath::parse('foo/')->isFile());
        $this->assertFalse(UrlPath::parse('/foo/')->isFile());
        $this->assertTrue(UrlPath::parse('foo/bar')->isFile());
        $this->assertTrue(UrlPath::parse('/foo/bar')->isFile());
    }

    /**
     * Test isDirectory method.
     */
    public function testIsDirectory()
    {
        $this->assertTrue(UrlPath::parse('')->isDirectory());
        $this->assertTrue(UrlPath::parse('/')->isDirectory());
        $this->assertFalse(UrlPath::parse('foo')->isDirectory());
        $this->assertFalse(UrlPath::parse('/foo')->isDirectory());
        $this->assertTrue(UrlPath::parse('foo/')->isDirectory());
        $this->assertTrue(UrlPath::parse('/foo/')->isDirectory());
        $this->assertFalse(UrlPath::parse('foo/bar')->isDirectory());
        $this->assertFalse(UrlPath::parse('/foo/bar')->isDirectory());
    }

    /**
     * Test getDirectory method.
     */
    public function testGetDirectory()
    {
        $this->assertSame('', UrlPath::parse('')->getDirectory()->__toString());
        $this->assertSame('/', UrlPath::parse('/')->getDirectory()->__toString());
        $this->assertSame('', UrlPath::parse('foo')->getDirectory()->__toString());
        $this->assertSame('/', UrlPath::parse('/foo')->getDirectory()->__toString());
        $this->assertSame('foo/', UrlPath::parse('foo/')->getDirectory()->__toString());
        $this->assertSame('/foo/', UrlPath::parse('/foo/')->getDirectory()->__toString());
        $this->assertSame('foo/', UrlPath::parse('foo/bar')->getDirectory()->__toString());
        $this->assertSame('/foo/', UrlPath::parse('/foo/bar')->getDirectory()->__toString());
        $this->assertSame('../', UrlPath::parse('../foo')->getDirectory()->__toString());
        $this->assertSame('../foo/', UrlPath::parse('../foo/')->getDirectory()->__toString());
        $this->assertSame('../foo/', UrlPath::parse('../foo/bar')->getDirectory()->__toString());
    }

    /**
     * Test getDepth method.
     */
    public function testGetDepth()
    {
        $this->assertSame(0, UrlPath::parse('')->getDepth());
        $this->assertSame(0, UrlPath::parse('/')->getDepth());
        $this->assertSame(0, UrlPath::parse('foo')->getDepth());
        $this->assertSame(0, UrlPath::parse('/foo')->getDepth());
        $this->assertSame(1, UrlPath::parse('foo/')->getDepth());
        $this->assertSame(1, UrlPath::parse('/foo/')->getDepth());
        $this->assertSame(1, UrlPath::parse('foo/bar')->getDepth());
        $this->assertSame(1, UrlPath::parse('/foo/bar')->getDepth());
        $this->assertSame(2, UrlPath::parse('foo/bar/')->getDepth());
        $this->assertSame(2, UrlPath::parse('/foo/bar/')->getDepth());
        $this->assertSame(-1, UrlPath::parse('../')->getDepth());
        $this->assertSame(-1, UrlPath::parse('../foo')->getDepth());
        $this->assertSame(-2, UrlPath::parse('../../foo')->getDepth());
        $this->assertSame(-1, UrlPath::parse('../../foo/')->getDepth());
    }

    /**
     * Test toRelative method.
     */
    public function testToRelative()
    {
        $this->assertSame('', UrlPath::parse('')->toRelative()->__toString());
        $this->assertSame('', UrlPath::parse('/')->toRelative()->__toString());
        $this->assertSame('foo', UrlPath::parse('foo')->toRelative()->__toString());
        $this->assertSame('foo', UrlPath::parse('/foo')->toRelative()->__toString());
        $this->assertSame('foo/bar', UrlPath::parse('foo/bar')->toRelative()->__toString());
        $this->assertSame('foo/bar', UrlPath::parse('/foo/bar')->toRelative()->__toString());
    }
}
