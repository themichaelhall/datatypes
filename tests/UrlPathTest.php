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
    }

    /**
     * Test getDirectoryParts method.
     */
    public function testGetDirectoryParts()
    {
        $this->assertSame([], UrlPath::parse('/')->getDirectoryParts());
        $this->assertSame(['foo'], UrlPath::parse('/foo/')->getDirectoryParts());
        $this->assertSame(['foo', 'bar'], UrlPath::parse('/foo/bar/baz.html')->getDirectoryParts());
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
}
