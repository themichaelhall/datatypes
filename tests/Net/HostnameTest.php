<?php

declare(strict_types=1);

namespace DataTypes\Tests\Net;

use DataTypes\Net\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Net\Hostname;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test Hostname class.
 */
class HostnameTest extends TestCase
{
    /**
     * Test __toString() method.
     */
    public function testToString()
    {
        self::assertSame('foo', Hostname::parse('foo')->__toString());
        self::assertSame('foo.com', Hostname::parse('foo.com')->__toString());
        self::assertSame('www.foo.com', Hostname::parse('www.foo.com')->__toString());
        self::assertSame('www.foo.bar.com', Hostname::parse('www.foo.bar.com')->__toString());
        self::assertSame('www.foo-bar.com', Hostname::parse('www.foo-bar.com')->__toString());
    }

    /**
     * Test that hostname is converted to lower case.
     */
    public function testHostnameIsLowerCase()
    {
        self::assertSame('www.bar.org', Hostname::parse('WWW.BAR.ORG')->__toString());
    }

    /**
     * Test that trailing dot in hostname is removed.
     */
    public function testTrailingDotInHostnameIsRemoved()
    {
        self::assertSame('www.bar.org', Hostname::parse('www.bar.org.')->__toString());
    }

    /**
     * Test that empty hostname is invalid.
     */
    public function testEmptyHostnameIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "" is empty.');

        Hostname::parse('');
    }

    /**
     * Test that hostname with only a dot is invalid.
     */
    public function testHostnameWithOnlyADotIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "." is invalid: Part of domain "" is empty.');

        Hostname::parse('.');
    }

    /**
     * Test that too long hostname is invalid.
     */
    public function testTooLongHostnameIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxx" is too long: Maximum allowed length is 255 characters.');

        Hostname::parse(
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.xxxxxxxxx.' .
            'xxxxxx'
        );
    }

    /**
     * Test that hostname with invalid character is invalid.
     */
    public function testHostnameWithInvalidCharacterIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "foo.ba+r.com" is invalid: Part of domain "ba+r" contains invalid character "+".');

        Hostname::parse('foo.ba+r.com');
    }

    /**
     * Test that hostname with empty part is invalid.
     */
    public function testHostnameWithEmptyPartIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "foo..com" is invalid: Part of domain "" is empty.');

        Hostname::parse('foo..com');
    }

    /**
     * Test that hostname with too long part is invalid.
     */
    public function testHostNameWithTooLongPartIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.com" is invalid: Part of domain "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is too long: Maximum allowed length is 63 characters.');

        Hostname::parse('foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.com');
    }

    /**
     * Test that hostname with part beginning with dash is invalid.
     */
    public function testHostnameWithPartBeginningWithDashIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "-foo.bar.com" is invalid: Part of domain "-foo" begins with "-".');

        Hostname::parse('-foo.bar.com');
    }

    /**
     * Test that hostname with part ending with dash is invalid.
     */
    public function testHostnameWithPartEndingWithDashIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "foo.bar-.com" is invalid: Part of domain "bar-" ends with "-".');

        Hostname::parse('foo.bar-.com');
    }

    /**
     * Test that hostname with empty top-level domain is invalid.
     */
    public function testHostnameWithEmptyTopLevelDomainIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "bar.." is invalid: Top-level domain "" is empty.');

        Hostname::parse('bar..');
    }

    /**
     * Test that hostname with too long top-level domain is invalid.
     */
    public function testHostNameWithTooLongTopLevelDomainIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is invalid: Top-level domain "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" is too long: Maximum allowed length is 63 characters.');

        Hostname::parse('foo.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
    }

    /**
     * Test that hostname with invalid character in top level domain is invalid.
     */
    public function testHostnameWithInvalidCharacterInTopLevelDomainIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Hostname "foo.bar.co2" is invalid: Top-level domain "co2" contains invalid character "2".');

        Hostname::parse('foo.bar.co2');
    }

    /**
     * Test getTld method.
     */
    public function testGetTld()
    {
        self::assertNull(Hostname::parse('foo')->getTld());
        self::assertSame('com', Hostname::parse('foo.com')->getTld());
        self::assertSame('org', Hostname::parse('foo.bar.org')->getTld());
    }

    /**
     * Test getDomainName method.
     */
    public function testGetDomainName()
    {
        self::assertSame('foo', Hostname::parse('foo')->getDomainName());
        self::assertSame('foo.com', Hostname::parse('foo.com')->getDomainName());
        self::assertSame('bar.org', Hostname::parse('foo.bar.org')->getDomainName());
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertFalse(Hostname::isValid(''));
        self::assertFalse(Hostname::isValid('.'));
        self::assertFalse(Hostname::isValid('bar..com'));
        self::assertTrue(Hostname::isValid('foo.bar.com.'));
        self::assertTrue(Hostname::isValid('FOO.BAR.COM.'));
        self::assertFalse(Hostname::isValid('foo..org'));
        self::assertFalse(Hostname::isValid('*.org'));
        self::assertFalse(Hostname::isValid('foo.[bar].org'));
        self::assertFalse(Hostname::isValid('[foo].bar.org'));
        self::assertFalse(Hostname::isValid('foo.bar..'));
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertNull(Hostname::tryParse(''));
        self::assertNull(Hostname::tryParse('.'));
        self::assertNull(Hostname::tryParse('bar..com'));
        self::assertSame('foo.bar.com', Hostname::tryParse('foo.bar.com.')->__toString());
        self::assertSame('foo.bar.com', Hostname::tryParse('FOO.BAR.COM.')->__toString());
        self::assertNull(Hostname::tryParse('foo..org'));
        self::assertNull(Hostname::tryParse('*.org'));
        self::assertNull(Hostname::tryParse('foo.[bar].org'));
        self::assertNull(Hostname::tryParse('[foo].bar.org'));
        self::assertNull(Hostname::tryParse('foo.bar..'));
    }

    /**
     * Test withTld method.
     */
    public function testWithTld()
    {
        self::assertSame('foo.com', Hostname::parse('foo')->withTld('com')->__toString());
        self::assertSame('foo.org', Hostname::parse('foo.com')->withTld('org')->__toString());
        self::assertSame('foo.bar.org', Hostname::parse('foo.bar.com')->withTld('org')->__toString());
        self::assertSame('foo.bar.org', Hostname::parse('FOO.BAR.COM')->withTld('ORG')->__toString());
    }

    /**
     * Test that a call to withTld method with an invalid top-level domain is invalid.
     */
    public function testWithTldWithInvalidTopDomainLevelIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Top-level domain "123" contains invalid character "1".');

        Hostname::parse('domain.com')->withTld('123');
    }

    /**
     * Test getDomainParts method.
     */
    public function testGetDomainParts()
    {
        self::assertSame(['foo'], Hostname::parse('foo')->getDomainParts());
        self::assertSame(['foo'], Hostname::parse('foo.com')->getDomainParts());
        self::assertSame(['bar', 'foo'], Hostname::parse('bar.foo.com')->getDomainParts());
    }

    /**
     * Test fromParts method.
     */
    public function testFromParts()
    {
        self::assertSame('foo', Hostname::fromParts(['foo'])->__toString());
        self::assertSame('foo.com', Hostname::fromParts(['foo'], 'com')->__toString());
        self::assertSame('bar.foo.com', Hostname::fromParts(['bar', 'foo'], 'com')->__toString());
        self::assertSame('bar.foo.com', Hostname::fromParts(['BAR', 'FOO'], 'COM')->__toString());
    }

    /**
     * Test that a call to fromParts with empty domain parts is invalid.
     */
    public function testFromPartsWithEmptyDomainPartsIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Domain parts [] is empty.');

        Hostname::fromParts([]);
    }

    /**
     * Test that a call to fromParts with invalid domain part is invalid.
     */
    public function testFromPartsWithInvalidDomainPartsIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Domain parts ["foo", "b*r"] is invalid: Part of domain "b*r" contains invalid character "*".');

        Hostname::fromParts(['foo', 'b*r']);
    }

    /**
     * Test that a call to fromParts with invalid top-level domain is invalid.
     */
    public function testFromPartsWithInvalidTopLevelDomainIsInvalid()
    {
        self::expectException(HostnameInvalidArgumentException::class);
        self::expectExceptionMessage('Top-level domain "c*m" contains invalid character "*".');

        Hostname::fromParts(['foo'], 'c*m');
    }

    /**
     * Test fromParts method with invalid argument type for domain part.
     */
    public function testFromPartsWithInvalidDomainPartArgumentType()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('$domainParts parameter is not an array of strings.');

        Hostname::fromParts(['foo', 98765], 'bar');
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(Hostname::parse('foo.bar.com.')->equals(Hostname::parse('foo.bar.com')));
        self::assertFalse(Hostname::parse('foo.bar.com')->equals(Hostname::parse('foo.bar.org')));
        self::assertTrue(Hostname::fromParts(['foo', 'bar'], 'com')->equals(Hostname::parse('foo.bar.com')));
        self::assertTrue(Hostname::fromParts(['foo'])->equals(Hostname::parse('foo')));
    }
}
