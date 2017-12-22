<?php

namespace DataTypes\Tests;

use DataTypes\IPAddress;

/**
 * Test IPAddress class.
 */
class IPAddressTest extends \PHPUnit_Framework_TestCase
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
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "" is empty.
     */
    public function testEmptyIPAddressIsInvalid()
    {
        IPAddress::parse('');
    }

    /**
     * Test that empty IP address with not four octets is invalid.
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "1.2.3" is invalid: IP address must consist of four octets.
     */
    public function testIPAddressWithNotFourOctetsIsInvalid()
    {
        IPAddress::parse('1.2.3');
    }

    /**
     * Test that IP address with empty octet is invalid.
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "192.168..1" is invalid: Octet "" is empty.
     */
    public function testIPAddressWithEmptyOctetIsInvalid()
    {
        IPAddress::parse('192.168..1');
    }

    /**
     * Test that IP address with invalid character in octet is invalid.
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "127.0.0X.1" is invalid: Octet "0X" contains invalid character "X".
     */
    public function testIPAddressWithInvalidCharacterInOctetIsInvalid()
    {
        IPAddress::parse('127.0.0X.1');
    }

    /**
     * Test that IP address with octet out of range is invalid.
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "255.255.256.255" is invalid: Octet 256 is out of range: Maximum value for an octet is 255.
     */
    public function testIPAddressWithOctetOutOfRangeIsInvalid()
    {
        IPAddress::parse('255.255.256.255');
    }

    /**
     * Test parse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $ipAddress parameter is not a string.
     */
    public function testParseWithInvalidArgumentType()
    {
        IPAddress::parse(1.0);
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
     * Test isValid method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $ipAddress parameter is not a string.
     */
    public function testIsValidWithInvalidArgumentType()
    {
        IPAddress::isValid(false);
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
     * Test tryParse method with invalid argument type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $ipAddress parameter is not a string.
     */
    public function testTryParseWithInvalidArgumentType()
    {
        /** @noinspection PhpParamsInspection */
        IPAddress::tryParse([192, 168, 1, 1]);
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
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage Octets are invalid: IP address must consist of four octets.
     */
    public function testFromPartsWithInvalidNumberOfOctets()
    {
        IPAddress::fromParts([1, 2, 3]);
    }

    /**
     * Test fromParts method with invalid octet type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $octet is not an integer.
     */
    public function testFromPartsWithInvalidOctetType()
    {
        IPAddress::fromParts([1, 'Foo', 3, 4]);
    }

    /**
     * Test fromParts method with octet value too low.
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage Octets are invalid: Octet -1 is out of range: Minimum value for an octet is 0.
     */
    public function testFromPartsWithOctetValueTooLow()
    {
        IPAddress::fromParts([1, 2, -1, 4]);
    }

    /**
     * Test fromParts method with octet value too high.
     *
     * @expectedException \DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage Octets are invalid: Octet 256 is out of range: Maximum value for an octet is 255.
     */
    public function testFromPartsWithOctetValueTooHigh()
    {
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

        if (PHP_INT_MAX > 2147483647) {
            // > 32 bit.
            self::assertSame(2147483648, IPAddress::parse('128.0.0.0')->toInteger());
            self::assertSame(3232235521, IPAddress::parse('192.168.0.1')->toInteger());
            self::assertSame(4294967295, IPAddress::parse('255.255.255.255')->toInteger());
        } else {
            // 32 bit.
            self::assertSame(intval(-2147483648), IPAddress::parse('128.0.0.0')->toInteger());
            self::assertSame(-1062731775, IPAddress::parse('192.168.0.1')->toInteger());
            self::assertSame(-1, IPAddress::parse('255.255.255.255')->toInteger());
        }
    }

    /**
     * Test fromInteger method.
     */
    public function testFromInteger()
    {
        self::assertSame('0.0.0.0', IPAddress::fromInteger(0)->__toString());
        self::assertSame('12.34.56.78', IPAddress::fromInteger(203569230)->__toString());
        self::assertSame('127.255.255.255', IPAddress::fromInteger(2147483647)->__toString());
        self::assertSame('128.0.0.0', IPAddress::fromInteger(intval(-2147483648))->__toString());
        self::assertSame('192.168.0.1', IPAddress::fromInteger(-1062731775)->__toString());
        self::assertSame('255.255.255.255', IPAddress::fromInteger(-1)->__toString());

        if (PHP_INT_MAX > 2147483647) {
            // > 32 bit.
            self::assertSame('128.0.0.0', IPAddress::fromInteger(2147483648)->__toString());
            self::assertSame('192.168.0.1', IPAddress::fromInteger(3232235521)->__toString());
            self::assertSame('255.255.255.255', IPAddress::fromInteger(4294967295)->__toString());
        }
    }

    /**
     * Test fromInteger method with invalid parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $i parameter is not an integer.
     */
    public function testFromIntegerWithInvalidParameterType()
    {
        IPAddress::fromInteger('Foo');
    }
}
