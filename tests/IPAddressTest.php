<?php

use DataTypes\IPAddress;

/**
 * Test IPAdress class.
 */
class IPAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('127.0.0.1', (new IPAddress('127.0.0.1'))->__toString());
        $this->assertSame('255.255.255.255', (new IPAddress('255.255.255.255'))->__toString());
    }

    /**
     * Test that empty IP address is invalid.
     *
     * @expectedException DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "" is empty.
     */
    public function testEmptyIPAddressIsInvalid()
    {
        new IPAddress('');
    }

    /**
     * Test that empty IP address with not four octets is invalid.
     *
     * @expectedException DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "1.2.3" is invalid: IP address must consist of four octets.
     */
    public function testIPAddressWithNotFourOctetsIsInvalid()
    {
        new IPAddress('1.2.3');
    }

    /**
     * Test that IP address with empty octet is invalid.
     *
     * @expectedException DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "192.168..1" is invalid: Octet "" is empty.
     */
    public function testIPAddressWithEmptyOctetIsInvalid()
    {
        new IPAddress('192.168..1');
    }

    /**
     * Test that IP address with invalid character in octet is invalid.
     *
     * @expectedException DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "127.0.0X.1" is invalid: Octet "0X" contains invalid character "X".
     */
    public function testIPAddressWithInvalidCharacterInOctetIsInvalid()
    {
        new IPAddress('127.0.0X.1');
    }

    /**
     * Test that IP address with octet out of range is invalid.
     *
     * @expectedException DataTypes\Exceptions\IPAddressInvalidArgumentException
     * @expectedExceptionMessage IP address "255.255.256.255" is invalid: Octet "256" is out of range: Maximum value for octet is 255.
     */
    public function testIPAddressWithOctetOutOfRangeIsInvalid()
    {
        new IPAddress('255.255.256.255');
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(IPAddress::isValid(''));
        $this->assertFalse(IPAddress::isValid('1.2.3'));
        $this->assertFalse(IPAddress::isValid('1.2.3.'));
        $this->assertFalse(IPAddress::isValid('256.1.1.1'));
        $this->assertFalse(IPAddress::isValid('yyy.123.234.1'));
        $this->assertTrue(IPAddress::isValid('0.0.0.0'));
        $this->assertTrue(IPAddress::isValid('255.255.255.255'));
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(IPAddress::tryParse(''));
        $this->assertNull(IPAddress::tryParse('1.2.3'));
        $this->assertNull(IPAddress::tryParse('1.2.3.'));
        $this->assertNull(IPAddress::tryParse('256.1.1.1'));
        $this->assertNull(IPAddress::tryParse('yyy.123.234.1'));
        $this->assertSame('0.0.0.0', IPAddress::tryParse('0.0.0.0')->__toString());
        $this->assertSame('255.255.255.255', IPAddress::tryParse('255.255.255.255')->__toString());
    }
}
