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
        $this->assertSame('foo', Hostname::parse('foo')->__toString());
        $this->assertSame('foo.com', Hostname::parse('foo.com')->__toString());
        $this->assertSame('www.foo.com', Hostname::parse('www.foo.com')->__toString());
        $this->assertSame('www.foo.bar.com', Hostname::parse('www.foo.bar.com')->__toString());
        $this->assertSame('www.foo-bar.com', Hostname::parse('www.foo-bar.com')->__toString());
    }

    /**
     * Test that hostname is converted to lower case.
     */
    public function testHostnameIsLowerCase()
    {
        $this->assertSame('www.bar.org', Hostname::parse('WWW.BAR.ORG')->__toString());
    }

    /**
     * Test that trailing dot in hostname is removed.
     */
    public function testTrailingDotInHostnameIsRemoved()
    {
        $this->assertSame('www.bar.org', Hostname::parse('www.bar.org.')->__toString());
    }

    /**
     * Test that empty hostname is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "" is empty.
     */
    public function testEmptyHostnameIsInvalid()
    {
        Hostname::parse('');
    }

    /**
     * Test that hostname with only a dot is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "." is invalid: Part of domain "" is empty.
     */
    public function testHostnameWithOnlyADotIsInvalid()
    {
        Hostname::parse('.');
    }

    /**
     * Test that too long hostname is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxx" is too long: Maximum allowed length is 255 characters.
     */
    public function testTooLongHostnameIsInvalid()
    {
        Hostname::parse(
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxx');
    }

    /**
     * Test that hostname with invalid character is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.ba+r.com" is invalid: Part of domain "ba+r" contains invalid character "+".
     */
    public function testHostnameWithInvalidCharacterIsInvalid()
    {
        Hostname::parse('foo.ba+r.com');
    }

    /**
     * Test that hostname with empty part is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo..com" is invalid: Part of domain "" is empty.
     */
    public function testHostnameWithEmptyPartIsInvalid()
    {
        Hostname::parse('foo..com');
    }

    /**
     * Test that hostname with too long part is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.com" is invalid: Part of domain "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is too long: Maximum allowed length is 63 characters.
     */
    public function testHostNameWithTooLongPartIsInvalid()
    {
        Hostname::parse('foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.com');
    }

    /**
     * Test that hostname with part beginning with dash is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "-foo.bar.com" is invalid: Part of domain "-foo" begins with "-".
     */
    public function testHostnameWithPartBeginningWithDashIsInvalid()
    {
        Hostname::parse('-foo.bar.com');
    }

    /**
     * Test that hostname with part ending with dash is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.bar-.com" is invalid: Part of domain "bar-" ends with "-".
     */
    public function testHostnameWithPartEndingWithDashIsInvalid()
    {
        Hostname::parse('foo.bar-.com');
    }

    /**
     * Test that hostname with empty top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "bar.." is invalid: Top-level domain "" is empty.
     */
    public function testHostnameWithEmptyTopLevelDomainIsInvalid()
    {
        Hostname::parse('bar..');
    }

    /**
     * Test that hostname with too long top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is invalid: Top-level domain "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is too long: Maximum allowed length is 63 characters.
     */
    public function testHostNameWithTooLongTopLevelDomainIsInvalid()
    {
        Hostname::parse('foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    /**
     * Test that hostname with invalid character in top level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.bar.co2" is invalid: Top-level domain "co2" contains invalid character "2".
     */
    public function testHostnameWithInvalidCharacterInTopLevelDomainIsInvalid()
    {
        Hostname::parse('foo.bar.co2');
    }

    /**
     * Test getTld method.
     */
    public function testGetTld()
    {
        $this->assertNull(Hostname::parse('foo')->getTld());
        $this->assertSame('com', Hostname::parse('foo.com')->getTld());
        $this->assertSame('org', Hostname::parse('foo.bar.org')->getTld());
    }

    /**
     * Test getDomainName method.
     */
    public function testGetDomainName()
    {
        $this->assertSame('foo', Hostname::parse('foo')->getDomainName());
        $this->assertSame('foo.com', Hostname::parse('foo.com')->getDomainName());
        $this->assertSame('bar.org', Hostname::parse('foo.bar.org')->getDomainName());
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
        $this->assertFalse(Hostname::isValid('*.org'));
        $this->assertFalse(Hostname::isValid('foo.[bar].org'));
        $this->assertFalse(Hostname::isValid('[foo].bar.org'));
        $this->assertFalse(Hostname::isValid('foo.bar..'));
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
        $this->assertNull(Hostname::tryParse('*.org'));
        $this->assertNull(Hostname::tryParse('foo.[bar].org'));
        $this->assertNull(Hostname::tryParse('[foo].bar.org'));
        $this->assertNull(Hostname::tryParse('foo.bar..'));
    }

    /**
     * Test withTld method.
     */
    public function testWithTld()
    {
        $this->assertSame('foo.com', Hostname::parse('foo')->withTld('com')->__toString());
        $this->assertSame('foo.org', Hostname::parse('foo.com')->withTld('org')->__toString());
        $this->assertSame('foo.bar.org', Hostname::parse('foo.bar.com')->withTld('org')->__toString());
        $this->assertSame('foo.bar.org', Hostname::parse('FOO.BAR.COM')->withTld('ORG')->__toString());
    }

    /**
     * Test that a call to withTld method with an invalid top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Top-level domain "123" contains invalid character "1".
     */
    public function testWithTldWithInvalidTopDomainLevelIsInvalid()
    {
        Hostname::parse('domain.com')->withTld('123');
    }

    /**
     * Test getDomainParts method.
     */
    public function testGetDomainParts()
    {
        $this->assertSame(['foo'], Hostname::parse('foo')->getDomainParts());
        $this->assertSame(['foo'], Hostname::parse('foo.com')->getDomainParts());
        $this->assertSame(['bar', 'foo'], Hostname::parse('bar.foo.com')->getDomainParts());
    }

    /**
     * Test fromParts method.
     */
    public function testFromParts()
    {
        $this->assertSame('foo', Hostname::fromParts(['foo'])->__toString());
        $this->assertSame('foo.com', Hostname::fromParts(['foo'], 'com')->__toString());
        $this->assertSame('bar.foo.com', Hostname::fromParts(['bar', 'foo'], 'com')->__toString());
        $this->assertSame('bar.foo.com', Hostname::fromParts(['BAR', 'FOO'], 'COM')->__toString());
    }

    /**
     * Test that a call to fromParts with empty domain parts is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Domain parts [] is empty.
     */
    public function testFromPartsWithEmptyDomainPartsIsInvalid()
    {
        Hostname::fromParts([]);
    }

    /**
     * Test that a call to fromParts with invalid domain part is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Domain parts ["foo", "b*r"] is invalid: Part of domain "b*r" contains invalid character "*".
     */
    public function testFromPartsWithInvalidDomainPartsIsInvalid()
    {
        Hostname::fromParts(['foo', 'b*r']);
    }

    /**
     * Test that a call to fromParts with invalid top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Top-level domain "c*m" contains invalid character "*".
     */
    public function testFromPartsWithInvalidTopLevelDomainIsInvalid()
    {
        Hostname::fromParts(['foo'], 'c*m');
    }
}
