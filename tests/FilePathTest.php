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
}
