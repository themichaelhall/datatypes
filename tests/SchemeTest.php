<?php

declare(strict_types=1);

namespace DataTypes\Tests;

use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Scheme;
use PHPUnit\Framework\TestCase;

/**
 * Test Scheme class.
 */
class SchemeTest extends TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        self::assertSame('http', Scheme::parse('http')->__toString());
        self::assertSame('https', Scheme::parse('https')->__toString());
    }

    /**
     * Test that empty scheme is invalid.
     */
    public function testEmptySchemeIsInvalid()
    {
        self::expectException(SchemeInvalidArgumentException::class);
        self::expectExceptionMessage('Scheme "" is empty.');

        Scheme::parse('');
    }

    /**
     * Test that invalid scheme is invalid.
     */
    public function testInvalidSchemeIsInvalid()
    {
        self::expectException(SchemeInvalidArgumentException::class);
        self::expectExceptionMessage('Scheme "foobar" is invalid: Scheme must be "http" or "https".');

        Scheme::parse('foobar');
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertFalse(Scheme::isValid(''));
        self::assertFalse(Scheme::isValid('foo'));
        self::assertTrue(Scheme::isValid('http'));
        self::assertTrue(Scheme::isValid('https'));
        self::assertTrue(Scheme::isValid('HTTPS'));
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertNull(Scheme::tryParse(''));
        self::assertNull(Scheme::tryParse('foo'));
        self::assertSame('http', Scheme::tryParse('http')->__toString());
        self::assertSame('https', Scheme::tryParse('https')->__toString());
        self::assertSame('https', Scheme::tryParse('HTTPS')->__toString());
    }

    /**
     * Test getType method.
     */
    public function testGetType()
    {
        self::assertSame(Scheme::TYPE_HTTP, Scheme::parse('http')->getType());
        self::assertSame(Scheme::TYPE_HTTPS, Scheme::parse('https')->getType());
    }

    /**
     * Test that scheme is converted to lower case.
     */
    public function testSchemeIsLowerCase()
    {
        self::assertSame('http', Scheme::parse('HTTP')->__toString());
    }

    /**
     * Test getDefaultPort method.
     */
    public function testGetDefaultPort()
    {
        self::assertSame(80, Scheme::parse('http')->getDefaultPort());
        self::assertSame(443, Scheme::parse('https')->getDefaultPort());
    }

    /**
     * Test isHttp method.
     */
    public function testIsHttp()
    {
        self::assertTrue(Scheme::parse('http')->isHttp());
        self::assertFalse(Scheme::parse('https')->isHttp());
    }

    /**
     * Test isHttps method.
     */
    public function testIsHttps()
    {
        self::assertFalse(Scheme::parse('http')->isHttps());
        self::assertTrue(Scheme::parse('https')->isHttps());
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(Scheme::parse('http')->equals(Scheme::parse('http')));
        self::assertFalse(Scheme::parse('http')->equals(Scheme::parse('https')));
        self::assertFalse(Scheme::parse('https')->equals(Scheme::parse('http')));
        self::assertTrue(Scheme::parse('https')->equals(Scheme::parse('https')));
    }
}
