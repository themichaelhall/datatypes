<?php

namespace DataTypes\Tests;

use DataTypes\EmailAddress;

/**
 * Test EmailAddress class.
 */
class EmailAddressTest extends \PHPUnit_Framework_TestCase
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
     * Test parse method with invalid parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $emailAddress parameter is not a string.
     */
    public function testParseWithInvalidParameterType()
    {
        EmailAddress::parse(false);
    }

    /**
     * Test that empty EmailAddress is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "" is empty.
     */
    public function testEmptyEmailAddressIsInvalid()
    {
        EmailAddress::parse('');
    }

    /**
     * Test that empty EmailAddress with missing @ is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "foo" is invalid: Character "@" is missing.
     */
    public function testEmailAddressWithMissingAtIsInvalid()
    {
        EmailAddress::parse('foo');
    }

    /**
     * Test that empty EmailAddress with empty host is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "foo@" is invalid: Hostname "" is empty.
     */
    public function testEmailAddressWithEmptyHostIsInvalid()
    {
        EmailAddress::parse('foo@');
    }

    /**
     * Test that empty EmailAddress with invalid host is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "foo@bar@baz" is invalid: Hostname "bar@baz" is invalid: Part of domain "bar@baz" contains invalid character "@".
     */
    public function testEmailAddressWithInvalidHostIsInvalid()
    {
        EmailAddress::parse('foo@bar@baz');
    }

    /**
     * Test that empty EmailAddress with empty username is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "@bar.com" is invalid: Username "" is empty.
     */
    public function testEmailAddressWithEmptyUsernameIsInvalid()
    {
        EmailAddress::parse('@bar.com');
    }

    /**
     * Test that empty EmailAddress with empty username is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "a"b(c)d,e:f;g>h<i[j\k]l@example.com" is invalid: Username "a"b(c)d,e:f;g>h<i[j\k]l" contains invalid character """.
     */
    public function testEmailAddressWithInvalidUsernameIsInvalid()
    {
        EmailAddress::parse('a"b(c)d,e:f;g>h<i[j\\k]l@example.com');
    }

    /**
     * Test that empty EmailAddress with empty username is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "12345678901234567890123456789012345678901234567890123456789012345@example.com" is invalid: Username "12345678901234567890123456789012345678901234567890123456789012345" is too long: Maximum length is 64.
     */
    public function testEmailAddressWithTooLongUsernameIsInvalid()
    {
        EmailAddress::parse('12345678901234567890123456789012345678901234567890123456789012345@example.com');
    }

    /**
     * Test that EmailAddress with username containing two consecutively dots is invalid.
     *
     * @expectedException \DataTypes\Exceptions\EmailAddressInvalidArgumentException
     * @expectedExceptionMessage Email address "foo..bar@example.com" is invalid: Username "foo..bar" contains "..".
     */
    public function testEmailAddressWithUsernameContainingTwoConsecutivelyDotsIsInvalid()
    {
        EmailAddress::parse('foo..bar@example.com');
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
    }

    /**
     * Test tryParse method with invalid parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $emailAddress parameter is not a string.
     */
    public function testTryParseWithInvalidParameterType()
    {
        EmailAddress::tryParse(false);
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
    }

    /**
     * Test isValid method with invalid parameter type.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $emailAddress parameter is not a string.
     */
    public function testIsValidWithInvalidParameterType()
    {
        EmailAddress::isValid(false);
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
}
