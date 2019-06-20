<?php

declare(strict_types=1);

namespace DataTypes\Tests;

use DataTypes\EmailAddress;
use DataTypes\Exceptions\EmailAddressInvalidArgumentException;
use DataTypes\Host;
use DataTypes\Hostname;
use DataTypes\IPAddress;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test EmailAddress class.
 */
class EmailAddressTest extends TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        self::assertSame('foo@domain.com', EmailAddress::parse('foo@domain.com')->__toString());
        self::assertSame('foo.bar@baz.domain.com', EmailAddress::parse('foo.bar@baz.domain.com')->__toString());
        self::assertSame('!#$%&\'*+-/=.?^_`{|}~@example.com', EmailAddress::parse('!#$%&\'*+-/=.?^_`{|}~@example.com')->__toString());
    }

    /**
     * Test that empty EmailAddress is invalid.
     */
    public function testEmptyEmailAddressIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "" is empty.');

        EmailAddress::parse('');
    }

    /**
     * Test that empty EmailAddress with missing @ is invalid.
     */
    public function testEmailAddressWithMissingAtIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "foo" is invalid: Character "@" is missing.');

        EmailAddress::parse('foo');
    }

    /**
     * Test that empty EmailAddress with empty host is invalid.
     */
    public function testEmailAddressWithEmptyHostIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "foo@" is invalid: Hostname "" is empty.');

        EmailAddress::parse('foo@');
    }

    /**
     * Test that empty EmailAddress with invalid host is invalid.
     */
    public function testEmailAddressWithInvalidHostIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "foo@bar@baz" is invalid: Hostname "bar@baz" is invalid: Part of domain "bar@baz" contains invalid character "@".');

        EmailAddress::parse('foo@bar@baz');
    }

    /**
     * Test that empty EmailAddress with empty username is invalid.
     */
    public function testEmailAddressWithEmptyUsernameIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "@bar.com" is invalid: Username "" is empty.');

        EmailAddress::parse('@bar.com');
    }

    /**
     * Test that empty EmailAddress with empty username is invalid.
     */
    public function testEmailAddressWithInvalidUsernameIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "a"b(c)d,e:f;g>h<i[j\k]l@example.com" is invalid: Username "a"b(c)d,e:f;g>h<i[j\k]l" contains invalid character """.');

        EmailAddress::parse('a"b(c)d,e:f;g>h<i[j\\k]l@example.com');
    }

    /**
     * Test that empty EmailAddress with empty username is invalid.
     */
    public function testEmailAddressWithTooLongUsernameIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "12345678901234567890123456789012345678901234567890123456789012345@example.com" is invalid: Username "12345678901234567890123456789012345678901234567890123456789012345" is too long: Maximum length is 64.');

        EmailAddress::parse('12345678901234567890123456789012345678901234567890123456789012345@example.com');
    }

    /**
     * Test that EmailAddress with username containing two consecutively dots is invalid.
     */
    public function testEmailAddressWithUsernameContainingTwoConsecutivelyDotsIsInvalid()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "foo..bar@example.com" is invalid: Username "foo..bar" contains "..".');

        EmailAddress::parse('foo..bar@example.com');
    }

    /**
     * Test parse EmailAddress with IP address.
     */
    public function testParseWithIpAddress()
    {
        $emailAddress = EmailAddress::parse('foo.bar@[127.0.0.1]');

        self::assertSame('foo.bar@[127.0.0.1]', $emailAddress->__toString());
        self::assertSame('127.0.0.1', $emailAddress->getHost()->getIPAddress()->__toString());
    }

    /**
     * Test parse EmailAddress with invalid IP address.
     */
    public function testParseWithInvalidIpAddress()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Email address "foo.bar@[127.0.X.1]" is invalid: IP address "127.0.X.1" is invalid: Octet "X" contains invalid character "X".');

        EmailAddress::parse('foo.bar@[127.0.X.1]');
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertSame('foo@domain.com', EmailAddress::tryParse('foo@domain.com')->__toString());
        self::assertSame('foo.bar@baz.domain.com', EmailAddress::tryParse('foo.bar@baz.domain.com')->__toString());
        self::assertNull(EmailAddress::tryParse(''));
        self::assertNull(EmailAddress::tryParse('foobar'));
        self::assertNull(EmailAddress::tryParse('foo.bar'));
        self::assertNull(EmailAddress::tryParse('foo.bar@'));
        self::assertNull(EmailAddress::tryParse('foo@bar@baz.com'));
        self::assertNull(EmailAddress::tryParse('@baz.com'));
        self::assertNull(EmailAddress::tryParse('a"b(c)d,e:f;g>h<i[j\\k]l@example.com'));
        self::assertSame('!#$%&\'*+-/=.?^_`{|}~@example.com', EmailAddress::tryParse('!#$%&\'*+-/=.?^_`{|}~@example.com')->__toString());
        self::assertNull(EmailAddress::tryParse('12345678901234567890123456789012345678901234567890123456789012345@example.com'));
        self::assertNull(EmailAddress::tryParse('foo..bar@example.com'));
        self::assertSame('foo.bar@[127.0.0.1]', EmailAddress::tryParse('foo.bar@[127.0.0.1]')->__toString());
        self::assertNull(EmailAddress::tryParse('foo.bar@[]'));
        self::assertNull(EmailAddress::tryParse('foo.bar@[1.2.3.256]'));
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        self::assertTrue(EmailAddress::isValid('foo@domain.com'));
        self::assertTrue(EmailAddress::isValid('foo.bar@baz.domain.com'));
        self::assertFalse(EmailAddress::isValid(''));
        self::assertFalse(EmailAddress::isValid('foo.bar'));
        self::assertFalse(EmailAddress::isValid('foo.bar@'));
        self::assertFalse(EmailAddress::isValid('foo@bar@baz.com'));
        self::assertFalse(EmailAddress::isValid('@baz.com'));
        self::assertFalse(EmailAddress::isValid('a"b(c)d,e:f;g>h<i[j\\k]l@example.com'));
        self::assertTrue(EmailAddress::isValid('!#$%&\'*+-/=.?^_`{|}~@example.com'));
        self::assertFalse(EmailAddress::isValid('12345678901234567890123456789012345678901234567890123456789012345@example.com'));
        self::assertFalse(EmailAddress::isValid('foo..bar@example.com'));
        self::assertTrue(EmailAddress::isValid('foo.bar@[127.0.0.1]'));
        self::assertFalse(EmailAddress::isValid('foo.bar@[]'));
        self::assertFalse(EmailAddress::isValid('foo.bar@[1.2.3.256]'));
    }

    /**
     * Test getHost method.
     */
    public function testGetHost()
    {
        $emailAddress = EmailAddress::parse('foo.bar@example.com');

        self::assertSame('example.com', $emailAddress->getHost()->__toString());
    }

    /**
     * Test getUsername method.
     */
    public function testGetUsername()
    {
        $emailAddress = EmailAddress::parse('foo.bar@example.com');

        self::assertSame('foo.bar', $emailAddress->getUsername());
    }

    /**
     * Test withUsername method.
     */
    public function testWithUsername()
    {
        $emailAddress = EmailAddress::parse('foo.bar@example.com');

        self::assertSame('baz@example.com', $emailAddress->withUsername('baz')->__toString());
    }

    /**
     * Test withUsername method with invalid username.
     */
    public function testWithUsernameWithInvalidUsername()
    {
        self::expectException(EmailAddressInvalidArgumentException::class);
        self::expectExceptionMessage('Username ">FooBar" contains invalid character ">".');

        $emailAddress = EmailAddress::parse('foo.bar@example.com');
        $emailAddress->withUsername('>FooBar');
    }

    /**
     * Test withHost method with host from hostname.
     */
    public function testWithHostWithHostFromHostname()
    {
        $emailAddress = EmailAddress::parse('foo.bar@example.com');

        self::assertSame('foo.bar@baz.org', $emailAddress->withHost(Host::fromHostname(Hostname::parse('baz.org')))->__toString());
    }

    /**
     * Test withHost method with host from IP-address.
     */
    public function testWithHostWithHostFromIpAddress()
    {
        $emailAddress = EmailAddress::parse('foo.bar@example.com');

        self::assertSame('foo.bar@[12.34.56.78]', $emailAddress->withHost(Host::fromIPAddress(IPAddress::parse('12.34.56.78')))->__toString());
    }

    /**
     * Test fromParts method with host from hostname.
     */
    public function testFromPartsWithHostFromHostname()
    {
        $emailAddress = EmailAddress::fromParts('foo.bar', Host::fromHostname(Hostname::parse('example.com')));

        self::assertSame('foo.bar@example.com', $emailAddress->__toString());
    }

    /**
     * Test fromParts method with host from IP-address.
     */
    public function testFromPartsWithHostFromIpAddress()
    {
        $emailAddress = EmailAddress::fromParts('foo.bar', Host::fromIPAddress(IPAddress::parse('12.34.56.78')));

        self::assertSame('foo.bar@[12.34.56.78]', $emailAddress->__toString());
    }

    /**
     * Test fromParts method with invalid username.
     */
    public function testFromPartsWithInvalidUsername()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Username "foo;bar" contains invalid character ";".');

        EmailAddress::fromParts('foo;bar', Host::parse('foo.com'));
    }

    /**
     * Test equals method.
     */
    public function testEquals()
    {
        self::assertTrue(EmailAddress::parse('foo.bar@example.com')->equals(EmailAddress::fromParts('foo.bar', Host::parse('example.com'))));
        self::assertFalse(EmailAddress::parse('foo.baz@example.com')->equals(EmailAddress::fromParts('foo.bar', Host::parse('example.com'))));
        self::assertFalse(EmailAddress::parse('foo.bar@example.com')->equals(EmailAddress::parse('foo.bar@example.org')));
    }
}
