<?php

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
}
