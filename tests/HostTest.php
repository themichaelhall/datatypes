<?php

use DataTypes\Host;

/**
 * Test Host class.
 */
class HostTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString() method.
     */
    public function testToString()
    {
        $this->assertSame('foo', Host::parse('foo')->__toString());
        $this->assertSame('www.foo.com', Host::parse('www.foo.com')->__toString());
        $this->assertSame('192.168.0.1', Host::parse('192.168.0.1')->__toString());
    }
}
