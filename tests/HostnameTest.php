<?php

use DataTypes\Hostname;

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

    /**
     * Test that empty hostname is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "" is empty.
     */
    public function testEmptyHostnameIsInvalid()
    {
        new Hostname('');
    }

    /**
     * Test that hostname with only a dot is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "." is invalid: Part of hostname "" is empty.
     */
    public function testHostnameWithOnlyADotIsInvalid()
    {
        new Hostname('.');
    }

    /**
     * Test that hostname with empty part is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo..com" is invalid: Part of hostname "" is empty.
     */
    public function testHostnameWithEmptyPartIsInvalid()
    {
        new Hostname('foo..com');
    }

    /**
     * Test getTld method.
     */
    public function testGetTld()
    {
        $this->assertNull((new Hostname('foo'))->getTld());
        $this->assertSame('com', (new Hostname('foo.com'))->getTld());
        $this->assertSame('org', (new Hostname('foo.bar.org'))->getTld());
    }

    /**
     * Test getDomain method.
     */
    public function testGetDomain()
    {
        $this->assertSame('foo', (new Hostname('foo'))->getDomain());
        $this->assertSame('foo.com', (new Hostname('foo.com'))->getDomain());
        $this->assertSame('bar.org', (new Hostname('foo.bar.org'))->getDomain());
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Hostname::isValid(''));
        $this->assertFalse(Hostname::isValid('.'));
        $this->assertFalse(Hostname::isValid('bar..com'));
        $this->assertTrue(Hostname::isValid('foo.bar.com.'));
        $this->assertTrue(Hostname::isValid('FOO.BAR.COM.'));
        $this->assertFalse(Hostname::isValid('foo..org'));
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(Hostname::tryParse(''));
        $this->assertNull(Hostname::tryParse('.'));
        $this->assertNull(Hostname::tryParse('bar..com'));
        $this->assertSame('foo.bar.com', Hostname::tryParse('foo.bar.com.')->__toString());
        $this->assertSame('foo.bar.com', Hostname::tryParse('FOO.BAR.COM.')->__toString());
        $this->assertNull(Hostname::tryParse('foo..org'));
    }
}
