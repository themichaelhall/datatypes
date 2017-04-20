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
     * Test parse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $urlPath parameter is not a string.
     */
    public function testParseWithInvalidArgumentType()
    {
        UrlPath::parse(1.0);
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
        $this->assertSame('/path%3F%21/file%3F%21', UrlPath::tryParse('/path%3f!/file%3f!')->__toString());
    }

    /**
     * Test tryParse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $urlPath parameter is not a string.
     */
    public function testTryParseWithInvalidArgumentType()
    {
        UrlPath::tryParse(true);
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
     * Test isValid method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $urlPath parameter is not a string.
     */
    public function testIsValidWithInvalidArgumentType()
    {
        UrlPath::isValid(42);
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

    /**
     * Test toAbsolute method.
     */
    public function testToAbsolute()
    {
        $this->assertSame('/', UrlPath::parse('')->toAbsolute()->__toString());
        $this->assertSame('/', UrlPath::parse('/')->toAbsolute()->__toString());
        $this->assertSame('/foo', UrlPath::parse('foo')->toAbsolute()->__toString());
        $this->assertSame('/foo', UrlPath::parse('/foo')->toAbsolute()->__toString());
        $this->assertSame('/foo/bar', UrlPath::parse('foo/bar')->toAbsolute()->__toString());
        $this->assertSame('/foo/bar', UrlPath::parse('/foo/bar')->toAbsolute()->__toString());
    }

    /**
     * Test that attempting to make an absolute path for a url path above root is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlPathLogicException
     * @expectedExceptionMessage Url path "../" can not be made absolute: Relative path is above base level.
     */
    public function testToAbsoluteForUrlPathAboveRootIsInvalid()
    {
        UrlPath::parse('../')->toAbsolute();
    }

    /**
     * Test withUrlPath method.
     */
    public function testWithUrlPath()
    {
        $this->assertSame('/bar', UrlPath::parse('/foo')->withUrlPath(UrlPath::parse('/bar'))->__toString());
        $this->assertSame('/bar', UrlPath::parse('foo')->withUrlPath(UrlPath::parse('/bar'))->__toString());
        $this->assertSame('/bar/baz', UrlPath::parse('/foo')->withUrlPath(UrlPath::parse('/bar/baz'))->__toString());
        $this->assertSame('/bar/baz', UrlPath::parse('foo')->withUrlPath(UrlPath::parse('/bar/baz'))->__toString());
        $this->assertSame('', UrlPath::parse('')->withUrlPath(UrlPath::parse(''))->__toString());
        $this->assertSame('/', UrlPath::parse('')->withUrlPath(UrlPath::parse('/'))->__toString());
        $this->assertSame('/foo/bar', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar'))->__toString());
        $this->assertSame('/foo/baz', UrlPath::parse('/foo/bar')->withUrlPath(UrlPath::parse('baz'))->__toString());
        $this->assertSame('foo/bar', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar'))->__toString());
        $this->assertSame('foo/baz', UrlPath::parse('foo/bar')->withUrlPath(UrlPath::parse('baz'))->__toString());
        $this->assertSame('/foo/bar/', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        $this->assertSame('foo/bar/', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        $this->assertSame('/foo/bar/', UrlPath::parse('/foo/baz')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        $this->assertSame('foo/bar/', UrlPath::parse('foo/baz')->withUrlPath(UrlPath::parse('bar/'))->__toString());
        $this->assertSame('/foo/bar/baz', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar/baz'))->__toString());
        $this->assertSame('foo/bar/baz', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar/baz'))->__toString());
        $this->assertSame('/foo/bar/baz/', UrlPath::parse('/foo/')->withUrlPath(UrlPath::parse('bar/baz/'))->__toString());
        $this->assertSame('foo/bar/baz/', UrlPath::parse('foo/')->withUrlPath(UrlPath::parse('bar/baz/'))->__toString());
        $this->assertSame('/foo/baz/file', UrlPath::parse('/foo/bar/')->withUrlPath(UrlPath::parse('../baz/file'))->__toString());
        $this->assertSame('foo/baz/file', UrlPath::parse('foo/bar/')->withUrlPath(UrlPath::parse('../baz/file'))->__toString());
        $this->assertSame('../foo/baz/file', UrlPath::parse('../foo/bar/')->withUrlPath(UrlPath::parse('../baz/file'))->__toString());
        $this->assertSame('/baz/file', UrlPath::parse('/foo/bar/')->withUrlPath(UrlPath::parse('../../baz/file'))->__toString());
        $this->assertSame('baz/file', UrlPath::parse('foo/bar/')->withUrlPath(UrlPath::parse('../../baz/file'))->__toString());
        $this->assertSame('../baz/file', UrlPath::parse('../foo/bar/')->withUrlPath(UrlPath::parse('../../baz/file'))->__toString());
        $this->assertSame('../baz/file', UrlPath::parse('foo/bar/')->withUrlPath(UrlPath::parse('../../../baz/file'))->__toString());
        $this->assertSame('../../baz/file', UrlPath::parse('../foo/bar/')->withUrlPath(UrlPath::parse('../../../baz/file'))->__toString());
    }

    /**
     * Test that combining an absolute url path with an url path that results in a path above root level is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlPathLogicException
     * @expectedExceptionMessage Url path "/foo/bar/" can not be combined with url path "../../../baz/file": Absolute path is above root level.
     */
    public function testAbsoluteUrlPathWithUrlPathAboveRootLevelIsInvalid()
    {
        UrlPath::parse('/foo/bar/')->withUrlPath(UrlPath::parse('../../../baz/file'));
    }

    /**
     * Test hasParentDirectory method.
     */
    public function hasParentDirectory()
    {
        $this->assertTrue(UrlPath::parse('')->hasParentDirectory());
        $this->assertFalse(UrlPath::parse('/')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('foo')->hasParentDirectory());
        $this->assertFalse(UrlPath::parse('/foo')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('foo/')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('/foo/')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('foo/bar')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('/foo/bar')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('foo/bar/')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('/foo/bar/')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('../')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('../foo')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('../../foo')->hasParentDirectory());
        $this->assertTrue(UrlPath::parse('../../foo/')->hasParentDirectory());
    }
}
