<?php

use DataTypes\Url;

/**
 * Test Url class.
 */
class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('http://www.domain.com/', Url::parse('http://www.domain.com/')->__toString());
    }

    /**
     * Test that empty Url is invalid.
     *
     * @expectedException DataTypes\Exceptions\UrlInvalidArgumentException
     * @expectedExceptionMessage Url "" is empty.
     */
    public function testEmptyUrlIsInvalid()
    {
        Url::parse('');
    }

    /**
     * Test isValid method.
     */
    public function testIsValid()
    {
        $this->assertFalse(Url::isValid(''));
        $this->assertTrue(Url::isValid('http://domain.com/'));
        // fixme: More tests
    }

    /**
     * Test tryParse method.
     */
    public function testTryParse()
    {
        $this->assertNull(Url::tryParse(''));
        $this->assertSame('http://domain.com/', Url::tryParse('http://domain.com/')->__toString());
        // fixme: More tests
    }
}
