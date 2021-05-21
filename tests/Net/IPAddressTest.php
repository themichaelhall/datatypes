<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net;

use DataTypes\Net\Exceptions\IPAddressInvalidArgumentException;
use DataTypes\Net\IPAddress;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test IPAddress class.
 */
class IPAddressTest extends TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        self::assertSame('127.0.0.1', IPAddress::parse('127.0.0.1')->__toString());
        self::assertSame('255.255.255.255', IPAddress::parse('255.255.255.255')->__toString());
    }

    /**
     * Test that empty IP address is invalid.
     */
    public function testEmptyIPAddressIsInvalid()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('IP address "" is empty.');

        IPAddress::parse('');
    }

    /**
     * Test that empty IP address with not four octets is invalid.
     */
    public function testIPAddressWithNotFourOctetsIsInvalid()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('IP address "1.2.3" is invalid: IP address must consist of four octets.');

        IPAddress::parse('1.2.3');
    }

    /**
     * Test that IP address with empty octet is invalid.
     */
    public function testIPAddressWithEmptyOctetIsInvalid()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('IP address "192.168..1" is invalid: Octet "" is empty.');

        IPAddress::parse('192.168..1');
    }

    /**
     * Test that IP address with invalid character in octet is invalid.
     */
    public function testIPAddressWithInvalidCharacterInOctetIsInvalid()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('IP address "127.0.0X.1" is invalid: Octet "0X" contains invalid character "X".');

        IPAddress::parse('127.0.0X.1');
    }

    /**
     * Test that IP address with octet out of range is invalid.
     */
    public function testIPAddressWithOctetOutOfRangeIsInvalid()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('IP address "255.255.256.255" is invalid: Octet 256 is out of range: Maximum value for an octet is 255.');

        IPAddress::parse('255.255.256.255');
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertFalse(IPAddress::isValid(''));
        self::assertFalse(IPAddress::isValid('1.2.3'));
        self::assertFalse(IPAddress::isValid('1.2.3.'));
        self::assertFalse(IPAddress::isValid('256.1.1.1'));
        self::assertFalse(IPAddress::isValid('yyy.123.234.1'));
        self::assertTrue(IPAddress::isValid('0.0.0.0'));
        self::assertTrue(IPAddress::isValid('255.255.255.255'));
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertNull(IPAddress::tryParse(''));
        self::assertNull(IPAddress::tryParse('1.2.3'));
        self::assertNull(IPAddress::tryParse('1.2.3.'));
        self::assertNull(IPAddress::tryParse('256.1.1.1'));
        self::assertNull(IPAddress::tryParse('yyy.123.234.1'));
        self::assertSame('0.0.0.0', IPAddress::tryParse('0.0.0.0')->__toString());
        self::assertSame('255.255.255.255', IPAddress::tryParse('255.255.255.255')->__toString());
    }

    /**
     * Test getParts method.
     */
    public function testGetParts()
    {
        self::assertSame([192, 168, 0, 1], IPAddress::parse('192.168.0.1')->getParts());
    }

    /**
     * Test fromParts method.
     */
    public function testFromParts()
    {
        self::assertSame('192.168.0.1', IPAddress::fromParts([192, 168, 0, 1])->__toString());
    }

    /**
     * Test fromParts method with invalid number of octets.
     */
    public function testFromPartsWithInvalidNumberOfOctets()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Octets are invalid: IP address must consist of four octets.');

        IPAddress::fromParts([1, 2, 3]);
    }

    /**
     * Test fromParts method with invalid octet type.
     */
    public function testFromPartsWithInvalidOctetType()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('$octets is not an array of integers.');

        IPAddress::fromParts([1, 'Foo', 3, 4]);
    }

    /**
     * Test fromParts method with octet value too low.
     */
    public function testFromPartsWithOctetValueTooLow()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Octets are invalid: Octet -1 is out of range: Minimum value for an octet is 0.');

        IPAddress::fromParts([1, 2, -1, 4]);
    }

    /**
     * Test fromParts method with octet value too high.
     */
    public function testFromPartsWithOctetValueTooHigh()
    {
        self::expectException(IPAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Octets are invalid: Octet 256 is out of range: Maximum value for an octet is 255.');

        IPAddress::fromParts([1, 2, 256, 4]);
    }

    /**
     * Test withMask method.
     */
    public function testWithMask()
    {
        self::assertSame('192.168.1.1', IPAddress::parse('192.168.1.1')->withMask(IPAddress::parse('255.255.255.255'))->__toString());
        self::assertSame('192.168.1.0', IPAddress::parse('192.168.1.1')->withMask(IPAddress::parse('255.255.255.0'))->__toString());
        self::assertSame('192.168.0.0', IPAddress::parse('192.168.1.1')->withMask(IPAddress::parse('255.255.0.0'))->__toString());
        self::assertSame('192.0.0.0', IPAddress::parse('192.168.1.1')->withMask(IPAddress::parse('255.0.0.0'))->__toString());
        self::assertSame('0.0.0.0', IPAddress::parse('192.168.1.1')->withMask(IPAddress::parse('0.0.0.0'))->__toString());
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(IPAddress::parse('1.2.3.4')->equals(IPAddress::parse('1.2.3.4')));
        self::assertFalse(IPAddress::parse('1.2.3.4')->equals(IPAddress::parse('1.2.3.0')));
        self::assertTrue(IPAddress::parse('127.0.0.1')->equals(IPAddress::fromParts([127, 0, 0, 1])));
    }

    /**
     * Test toInteger method.
     */
    public function testToInteger()
    {
        self::assertSame(0, IPAddress::parse('0.0.0.0')->toInteger());
        self::assertSame(203569230, IPAddress::parse('12.34.56.78')->toInteger());
        self::assertSame(2147483647, IPAddress::parse('127.255.255.255')->toInteger());
        self::assertSame(2147483648, IPAddress::parse('128.0.0.0')->toInteger());
        self::assertSame(3232235521, IPAddress::parse('192.168.0.1')->toInteger());
        self::assertSame(4294967295, IPAddress::parse('255.255.255.255')->toInteger());
    }

    /**
     * Test fromInteger method.
     */
    public function testFromInteger()
    {
        self::assertSame('0.0.0.0', IPAddress::fromInteger(0)->__toString());
        self::assertSame('12.34.56.78', IPAddress::fromInteger(203569230)->__toString());
        self::assertSame('127.255.255.255', IPAddress::fromInteger(2147483647)->__toString());
        self::assertSame('128.0.0.0', IPAddress::fromInteger(-2147483648)->__toString());
        self::assertSame('192.168.0.1', IPAddress::fromInteger(-1062731775)->__toString());
        self::assertSame('255.255.255.255', IPAddress::fromInteger(-1)->__toString());
        self::assertSame('128.0.0.0', IPAddress::fromInteger(2147483648)->__toString());
        self::assertSame('192.168.0.1', IPAddress::fromInteger(3232235521)->__toString());
        self::assertSame('255.255.255.255', IPAddress::fromInteger(4294967295)->__toString());
    }
}
