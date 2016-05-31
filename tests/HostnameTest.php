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
}
