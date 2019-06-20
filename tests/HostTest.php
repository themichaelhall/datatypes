<?php

declare(strict_types=1);

namespace DataTypes\Tests;

use DataTypes\Exceptions\HostInvalidArgumentException;
use DataTypes\Host;
use DataTypes\Hostname;
use DataTypes\IPAddress;
use PHPUnit\Framework\TestCase;

/**
 * Test Host class.
 */
class HostTest extends TestCase
{
    /**
     * Test __toString() method.
     */
    public function testToString()
    {
        self::assertSame('foo', Host::parse('foo')->__toString());
        self::assertSame('www.foo.com', Host::parse('www.foo.com')->__toString());
        self::assertSame('192.168.0.1', Host::parse('192.168.0.1')->__toString());
    }

    /**
     * Test that empty host is invalid.
     */
    public function testEmptyHostIsInvalid()
    {
        self::expectException(HostInvalidArgumentException::class);
        self::expectExceptionMessage('Host "" is empty.');

        Host::parse('');
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertNull(Host::tryParse(''));
        self::assertSame('domain.com', Host::tryParse('domain.com')->__toString());
        self::assertSame('1.2.3.4', Host::tryParse('1.2.3.4')->__toString());
        self::assertNull(Host::tryParse('*'));
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertFalse(Host::isValid(''));
        self::assertTrue(Host::isValid('domain.com'));
        self::assertTrue(Host::isValid('1.2.3.4'));
        self::assertFalse(Host::isValid('*'));
    }

    /**
     * Test that invalid hostname or invalid IP address is invalid.
     */
    public function testInvalidHostnameOrInvalidIPAddressIsInvalid()
    {
        self::expectException(HostInvalidArgumentException::class);
        self::expectExceptionMessage('Host "foo@bar.com" is invalid: Hostname "foo@bar.com" is invalid: Part of domain "foo@bar" contains invalid character "@".');

        Host::parse('foo@bar.com');
    }

    /**
     * Test fromHostname method.
     */
    public function testFromHostname()
    {
        self::assertSame('www.bar.org', Host::fromHostname(Hostname::parse('www.bar.org'))->__toString());
        self::assertSame('www.bar.org', Host::fromHostname(Hostname::parse('WWW.BAR.ORG'))->__toString());
    }

    /**
     * Test fromIPAddress method.
     */
    public function testFromIPAddress()
    {
        self::assertSame('10.20.30.40', Host::fromIPAddress(IPAddress::parse('10.20.30.40'))->__toString());
    }

    /**
     * Test getIPAddress method.
     */
    public function testGetIPAddress()
    {
        self::assertNull(Host::parse('foo.bar.com')->getIPAddress());
        self::assertSame('10.20.30.40', Host::parse('10.20.30.40')->getIPAddress()->__toString());
    }

    /**
     * Test getHostname method.
     */
    public function testGetHostname()
    {
        self::assertSame('domain.com', Host::parse('domain.com')->getHostname()->__toString());
        self::assertSame('40.30.20.10.in-addr.arpa', Host::parse('10.20.30.40')->getHostname()->__toString());
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(Host::parse('www.example.com')->equals(Host::fromHostname(Hostname::parse('www.example.com'))));
        self::assertFalse(Host::parse('www.example.com')->equals(Host::fromHostname(Hostname::parse('www.example.org'))));
        self::assertTrue(Host::parse('127.0.0.1')->equals(Host::fromIPAddress(IPAddress::parse('127.0.0.1'))));
        self::assertFalse(Host::parse('localhost')->equals(Host::fromIPAddress(IPAddress::parse('127.0.0.1'))));
        self::assertFalse(Host::parse('127.0.0.1')->equals(Host::parse('example.com')));
        self::assertTrue(Host::parse('1.0.0.127.in-addr.arpa')->equals(Host::fromIPAddress(IPAddress::parse('127.0.0.1'))));
    }
}
