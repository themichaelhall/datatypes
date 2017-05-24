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
}
