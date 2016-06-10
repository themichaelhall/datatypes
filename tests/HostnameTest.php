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
        $this->assertSame('foo', (new Hostname('foo'))->__toString());
        $this->assertSame('foo.com', (new Hostname('foo.com'))->__toString());
        $this->assertSame('www.foo.com', (new Hostname('www.foo.com'))->__toString());
        $this->assertSame('www.foo.bar.com', (new Hostname('www.foo.bar.com'))->__toString());
        $this->assertSame('www.foo-bar.com', (new Hostname('www.foo-bar.com'))->__toString());
    }

    /**
     * Test that hostname is converted to lower case.
     */
    public function testHostnameIsLowerCase()
    {
        $this->assertSame('www.bar.org', (new Hostname('WWW.BAR.ORG'))->__toString());
    }

    /**
     * Test that trailing dot in hostname is removed.
     */
    public function testTrailingDotInHostnameIsRemoved()
    {
        $this->assertSame('www.bar.org', (new Hostname('www.bar.org.'))->__toString());
    }

    /**
     * Test that empty hostname is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "" is empty.
     */
    public function testEmptyHostnameIsInvalid()
    {
        new Hostname('');
    }

    /**
     * Test that hostname with only a dot is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "." is invalid: Part of hostname "" is empty.
     */
    public function testHostnameWithOnlyADotIsInvalid()
    {
        new Hostname('.');
    }

    /**
     * Test that too long hostname is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxx" is too long: Maximum allowed length is 255 characters.
     */
    public function testTooLongHostnameIsInvalid()
    {
        new Hostname(
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
     * @expectedExceptionMessage Hostname "foo.ba+r.com" is invalid: Part of hostname "ba+r" contains invalid character "+".
     */
    public function testHostnameWithInvalidCharacterIsInvalid()
    {
        new Hostname('foo.ba+r.com');
    }

    /**
     * Test that hostname with empty part is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo..com" is invalid: Part of hostname "" is empty.
     */
    public function testHostnameWithEmptyPartIsInvalid()
    {
        new Hostname('foo..com');
    }

    /**
     * Test that hostname with too long part is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.com" is invalid: Part of hostname "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is too long: Maximum allowed length is 63 characters.
     */
    public function testHostNameWithTooLongPartIsInvalid()
    {
        new Hostname('foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.com');
    }

    /**
     * Test that hostname with part beginning with dash is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "-foo.bar.com" is invalid: Part of hostname "-foo" begins with "-".
     */
    public function testHostnameWithPartBeginningWithDashIsInvalid()
    {
        new Hostname('-foo.bar.com');
    }

    /**
     * Test that hostname with part ending with dash is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.bar-.com" is invalid: Part of hostname "bar-" ends with "-".
     */
    public function testHostnameWithPartEndingWithDashIsInvalid()
    {
        new Hostname('foo.bar-.com');
    }

    /**
     * Test that hostname with empty top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "bar.." is invalid: Top-level domain "" is empty.
     */
    public function testHostnameWithEmptyTopLevelDomainIsInvalid()
    {
        new Hostname('bar..');
    }

    /**
     * Test that hostname with too long top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is invalid: Top-level domain "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is too long: Maximum allowed length is 63 characters.
     */
    public function testHostNameWithTooLongTopLevelDomainIsInvalid()
    {
        new Hostname('foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    /**
     * Test that hostname with invalid character in top level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Hostname "foo.bar.co2" is invalid: Top-level domain "co2" contains invalid character "2".
     */
    public function testHostnameWithInvalidCharacterInTopLevelDomainIsInvalid()
    {
        new Hostname('foo.bar.co2');
    }

    /**
     * Test getTld method.
     */
    public function testGetTld()
    {
        $this->assertNull((new Hostname('foo'))->getTld());
        $this->assertSame('com', (new Hostname('foo.com'))->getTld());
        $this->assertSame('org', (new Hostname('foo.bar.org'))->getTld());
    }

    /**
     * Test getDomain method.
     */
    public function testGetDomain()
    {
        $this->assertSame('foo', (new Hostname('foo'))->getDomain());
        $this->assertSame('foo.com', (new Hostname('foo.com'))->getDomain());
        $this->assertSame('bar.org', (new Hostname('foo.bar.org'))->getDomain());
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
        $this->assertSame('foo.com', Hostname::tryParse('foo')->withTld('com')->__toString());
        $this->assertSame('foo.org', Hostname::tryParse('foo.com')->withTld('org')->__toString());
        $this->assertSame('foo.bar.org', Hostname::tryParse('foo.bar.com')->withTld('org')->__toString());
    }

    /**
     * Test that a call to withTld method with an invalid top-level domain is invalid.
     *
     * @expectedException DataTypes\Exceptions\HostnameInvalidArgumentException
     * @expectedExceptionMessage Top-level domain "123" contains invalid character "1".
     */
    public function testWithTldWithInvalidTopDomainLevelIsInvalid()
    {
        Hostname::tryParse('domain.com')->withTld('123');
    }
}
