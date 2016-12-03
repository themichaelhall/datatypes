<?php

use DataTypes\Scheme;

/**
 * Test Scheme class.
 */
class SchemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('http', Scheme::parse('http')->__toString());
        $this->assertSame('https', Scheme::parse('https')->__toString());
    }

    /**
     * Test that empty scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\SchemeInvalidArgumentException
     * @expectedExceptionMessage Scheme "" is empty.
     */
    public function testEmptySchemeIsInvalid()
    {
        Scheme::parse('');
    }

    /**
     * Test that invalid scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\SchemeInvalidArgumentException
     * @expectedExceptionMessage Scheme "foobar" is invalid: Scheme must be "http" or "https".
     */
    public function testInvalidSchemeIsInvalid()
    {
        Scheme::parse('foobar');
    }

    /**
     * Test parse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $scheme parameter is not a string.
     */
    public function testParseWithInvalidArgumentType()
    {
        Scheme::parse(['https']);
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Scheme::isValid(''));
        $this->assertFalse(Scheme::isValid('foo'));
        $this->assertTrue(Scheme::isValid('http'));
        $this->assertTrue(Scheme::isValid('https'));
        $this->assertTrue(Scheme::isValid('HTTPS'));
    }

    /**
     * Test isValid method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $scheme parameter is not a string.
     */
    public function testIsValidWithInvalidArgumentType()
    {
        Scheme::isValid(true);
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(Scheme::tryParse(''));
        $this->assertNull(Scheme::tryParse('foo'));
        $this->assertSame('http', Scheme::tryParse('http')->__toString());
        $this->assertSame('https', Scheme::tryParse('https')->__toString());
        $this->assertSame('https', Scheme::tryParse('HTTPS')->__toString());
    }

    /**
     * Test tryParse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $scheme parameter is not a string.
     */
    public function testTryParseWithInvalidArgumentType()
    {
        Scheme::tryParse(1.2);
    }

    /**
     * Test getType method.
     */
    public function testGetType()
    {
        $this->assertSame(Scheme::TYPE_HTTP, Scheme::parse('http')->getType());
        $this->assertSame(Scheme::TYPE_HTTPS, Scheme::parse('https')->getType());
    }

    /**
     * Test that scheme is converted to lower case.
     */
    public function testSchemeIsLowerCase()
    {
        $this->assertSame('http', Scheme::parse('HTTP')->__toString());
    }

    /**
     * Test getDefaultPort method.
     */
    public function testGetDefaultPort()
    {
        $this->assertSame(80, Scheme::parse('http')->getDefaultPort());
        $this->assertSame(443, Scheme::parse('https')->getDefaultPort());
    }

    /**
     * Test isHttp method.
     */
    public function testIsHttp()
    {
        $this->assertTrue(Scheme::parse('http')->isHttp());
        $this->assertFalse(Scheme::parse('https')->isHttp());
    }

    /**
     * Test isHttps method.
     */
    public function testIsHttps()
    {
        $this->assertFalse(Scheme::parse('http')->isHttps());
        $this->assertTrue(Scheme::parse('https')->isHttps());
    }
}
