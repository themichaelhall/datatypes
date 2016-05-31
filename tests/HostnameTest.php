<?php

use MichaelHall\DataTypes\Hostname;

/**
 * Test Hostname class.
 */
class HostnameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString() method.
     */
    public function testToString()
    {
        $this->assertSame('foo', (new Hostname('foo'))->__toString());
        $this->assertSame('foo.com', (new Hostname('foo.com'))->__toString());
        $this->assertSame('www.foo.com', (new Hostname('www.foo.com'))->__toString());
    }

    /**
     * Test that hostname is converted to lower case.
     */
    public function testHostnameIsLowerCase()
    {
        $this->assertSame('www.bar.org', (new Hostname('WWW.BAR.ORG'))->__toString());
    }

    /**
     * Test that trailing dot in hostname is removed.
     */
    public function testTrailingDotInHostnameIsRemoved()
    {
        $this->assertSame('www.bar.org', (new Hostname('www.bar.org.'))->__toString());
    }
}
