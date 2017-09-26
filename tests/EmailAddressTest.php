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
    public function testEmptyEmailIsInvalid()
    {
        EmailAddress::parse('');
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        self::assertSame('foo@domain.com', EmailAddress::tryParse('foo@domain.com')->__toString());
        self::assertSame('foo.bar@baz.domain.com', EmailAddress::tryParse('foo.bar@baz.domain.com')->__toString());
        self::assertNull(EmailAddress::tryParse(''));
        // fixme: more tests
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
}
