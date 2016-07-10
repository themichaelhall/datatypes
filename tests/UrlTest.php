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
        $this->assertSame('http://www.domain.com/FOO/BAR', Url::parse('HTTP://WWW.DOMAIN.COM/FOO/BAR')->__toString());
        $this->assertSame('http://www.domain.com:1234/', Url::parse('http://www.domain.com:1234/')->__toString());
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
        $this->assertSame('http://foo.bar.com:443/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('http'), false)->__toString());
        $this->assertSame('https://foo.bar.com/path/', Url::parse('https://foo.bar.com/path/')->withScheme(Scheme::parse('https'), false)->__toString());
        $this->assertSame('http://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('http'))->__toString());
        $this->assertSame('https://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('https'))->__toString());
        $this->assertSame('http://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('http'), false)->__toString());
        $this->assertSame('https://foo.bar.com:1000/path/', Url::parse('https://foo.bar.com:1000/path/')->withScheme(Scheme::parse('https'), false)->__toString());
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
     * @expectedExceptionMessage Url "https://foo@bar" is invalid: Host "foo@bar" is invalid: Hostname "foo@bar" is invalid: Part of domain "foo@bar" contains invalid character "@".
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
     * Test getPort method.
     */
    public function testGetPort()
    {
        $this->assertSame(80, Url::parse('http://foo.bar.com/path/')->getPort());
        $this->assertSame(443, Url::parse('https://foo.bar.com/path/')->getPort());
        $this->assertSame(1000, Url::parse('https://foo.bar.com:1000/path/')->getPort());
    }

    /**
     * Test that url with invalid character in port is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "http://domain.com:12X45" is invalid: Port "12X45" contains invalid character "X".
     */
    public function testUrlWithInvalidCharacterInPortIsInvalid()
    {
        Url::parse('http://domain.com:12X45');
    }

    /**
     * Test that url with port out of range is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "http://domain.com:65536" is invalid: Port "65536" is out of range: Maximum port number is 65535.
     */
    public function testUrlWithPortOutOfRangeIsInvalid()
    {
        Url::parse('http://domain.com:65536');
    }

    /**
     * Test getPath method.
     */
    public function testGetPath()
    {
        $this->assertSame('/', Url::parse('http://foo.com/')->getPath()->__toString());
        $this->assertSame('/foo/bar', Url::parse('http://domain.com/foo/bar')->getPath()->__toString());
    }

    /**
     * Test that url with invalid path is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "https://domain.com:1000/foo/{bar}" is invalid: Url path "/foo/{bar}" is invalid: Filename "{bar}" contains invalid character "{".
     */
    public function testUrlWithInvalidPathIsInvalid()
    {
        Url::parse('https://domain.com:1000/foo/{bar}');
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
        $this->assertFalse(Url::isValid('http://domain.com:XXX/'));
        $this->assertTrue(Url::isValid('http://domain.com:1234/'));
        $this->assertFalse(Url::isValid('http://domain.com:1234/{foo}'));
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
        $this->assertNull(Url::tryParse('http://domain.com:XXX/'));
        $this->assertSame('http://domain.com:1234/', Url::tryParse('http://domain.com:1234/')->__toString());
        $this->assertNull(Url::tryParse('http://domain.com:1234/{foo}'));
        // fixme: More tests
    }
}
