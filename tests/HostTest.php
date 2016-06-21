<?php

use DataTypes\Host;
use DataTypes\Hostname;
use DataTypes\IPAddress;

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
        $this->assertNull(Host::tryParse('*'));
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Host::isValid(''));
        $this->assertTrue(Host::isValid('domain.com'));
        $this->assertTrue(Host::isValid('1.2.3.4'));
        $this->assertFalse(Host::isValid('*'));
    }

    /**
     * Test that invalid hostname or invalid IP address is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostInvalidArgumentException
     * @expectedExceptionMessage Host "foo@bar.com" is invalid: Hostname "foo@bar.com" is invalid: Part of domain "foo@bar" contains invalid character "@".
     */
    public function testInvalidHostnameOrInvalidIPAddressIsInvalid()
    {
        Host::parse('foo@bar.com');
    }

    /**
     * Test fromHostname method.
     */
    public function testFromHostname()
    {
        $this->assertSame('www.bar.org', Host::fromHostname(Hostname::parse('www.bar.org'))->__toString());
        $this->assertSame('www.bar.org', Host::fromHostname(Hostname::parse('WWW.BAR.ORG'))->__toString());
    }

    /**
     * Test fromIPAddress method.
     */
    public function testFromIPAddress()
    {
        $this->assertSame('10.20.30.40', Host::fromIPAddress(IPAddress::parse('10.20.30.40'))->__toString());
    }

    public function testGetIPAddress()
    {
        $this->assertNull(Host::parse('foo.bar.com')->getIPAddress());
        $this->assertSame('10.20.30.40', Host::parse('10.20.30.40')->getIPAddress()->__toString());
    }
}
