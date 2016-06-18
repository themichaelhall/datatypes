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

    /**
     * Test that empty host is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostInvalidArgumentException
     * @expectedExceptionMessage Host "" is empty.
     */
    public function testEmptyHostIsInvalid()
    {
        Host::parse('');
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(Host::tryParse(''));
        $this->assertSame('domain.com', Host::tryParse('domain.com')->__toString());
        $this->assertSame('1.2.3.4', Host::tryParse('1.2.3.4')->__toString());
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Host::isValid(''));
        $this->assertTrue(Host::isValid('domain.com'));
        $this->assertTrue(Host::isValid('1.2.3.4'));
    }
}
