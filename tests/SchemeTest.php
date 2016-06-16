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
        $this->assertSame('http', (new Scheme('http'))->__toString());
        $this->assertSame('https', (new Scheme('https'))->__toString());
    }

    /**
     * Test that empty scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\SchemeInvalidArgumentException
     * @expectedExceptionMessage Scheme "" is empty.
     */
    public function testEmptySchemeIsInvalid()
    {
        new Scheme('');
    }

    /**
     * Test that invalid scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\SchemeInvalidArgumentException
     * @expectedExceptionMessage Scheme "foobar" is invalid: Scheme must be "http" or "https"
     */
    public function testInvalidSchemeIsInvalid()
    {
        new Scheme('foobar');
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
    }

    /**
     * Test getType method.
     */
    public function testGetType()
    {
        $this->assertSame(Scheme::TYPE_HTTP, Scheme::tryParse('http')->getType());
        $this->assertSame(Scheme::TYPE_HTTPS, Scheme::tryParse('https')->getType());
    }
}
