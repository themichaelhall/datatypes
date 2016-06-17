<?php

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
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Url::isValid(''));
        $this->assertFalse(Url::isValid('foo://bar.com/'));
        $this->assertTrue(Url::isValid('http://domain.com/'));
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
        // fixme: More tests
    }
}
