<?php

use DataTypes\Host;
use DataTypes\Scheme;
use DataTypes\Url;

/**
 * Test Url class.
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('http://www.domain.com/', Url::parse('http://www.domain.com/')->__toString());
        $this->assertSame('http://www.domain.com/', Url::parse('http://www.domain.com')->__toString());
        $this->assertSame('http://www.domain.com/foo/Bar', Url::parse('http://www.domain.com/foo/Bar')->__toString());
    }

    /**
     * Test that empty Url is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "" is empty.
     */
    public function testEmptyUrlIsInvalid()
    {
        Url::parse('');
    }

    /**
     * Test getScheme method.
     */
    public function testGetScheme()
    {
        $this->assertSame(Scheme::TYPE_HTTP, Url::parse('http://foo.bar.com/')->getScheme()->getType());
        $this->assertSame(Scheme::TYPE_HTTPS, Url::parse('https://foo.bar.com/')->getScheme()->getType());
    }

    /**
     * Test that missing scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "foo.bar.com" is invalid: Scheme is missing.
     */
    public function testMissingSchemeIsInvalid()
    {
        Url::parse('foo.bar.com');
    }

    /**
     * Test that empty scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "://foo.bar.com/" is invalid: Scheme "" is empty.
     */
    public function testEmptySchemeIsInvalid()
    {
        Url::parse('://foo.bar.com/');
    }

    /**
     * Test that invalid scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "baz://foo.bar.com/" is invalid: Scheme "baz" is invalid: Scheme must be "http" or "https"
     */
    public function testInvalidSchemeIsInvalid()
    {
        Url::parse('baz://foo.bar.com/');
    }

    /**
     * Test withScheme method.
     */
    public function testWithScheme()
    {
        $this->assertSame('http://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('http'))->__toString());
        $this->assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('https'))->__toString());
    }

    /**
     * Test getHost method.
     */
    public function testGetHost()
    {
        $this->assertSame('foo.bar.com', Url::parse('http://foo.bar.com/path/')->getHost()->__toString());
        $this->assertSame('10.10.10.10', Url::parse('http://10.10.10.10/')->getHost()->__toString());
        $this->assertSame('10.10.10.10', Url::parse('http://10.10.10.10')->getHost()->__toString());
    }

    /**
     * Test that empty host is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://" is invalid: Host "" is empty.
     */
    public function testEmptyHostIsInvalid()
    {
        Url::parse('https://');
    }

    /**
     * Test that invalid host is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://foo@bar" is invalid: Host "foo@bar" is invalid: Hostname "foo@bar" is invalid: Part of hostname "foo@bar" contains invalid character "@".
     */
    public function testInvalidHostIsInvalid()
    {
        Url::parse('https://foo@bar');
    }

    /**
     * Test withHost method.
     */
    public function testWithHost()
    {
        $this->assertSame('http://foo.org/path/', Url::parse('http://192.168.0.1/path/')->withHost(Host::parse('foo.org'))->__toString());
        $this->assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withHost(Host::parse('foo.bar.com'))->__toString());
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Url::isValid(''));
        $this->assertFalse(Url::isValid('foo://bar.com/'));
        $this->assertTrue(Url::isValid('http://domain.com/'));
        $this->assertFalse(Url::isValid('http:///path/'));
        $this->assertFalse(Url::isValid('http://+++/'));
        // fixme: More tests
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(Url::tryParse(''));
        $this->assertNull(Url::tryParse('foo://bar.com/'));
        $this->assertSame('http://domain.com/', Url::tryParse('http://domain.com/')->__toString());
        $this->assertNull(Url::tryParse('http:///path/'));
        $this->assertNull(Url::tryParse('http://+++/'));
        // fixme: More tests
    }
}
