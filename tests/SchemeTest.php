<?php

use DataTypes\Scheme;

/**
 * Test Scheme class.
 */
class SchemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('http', (new Scheme('http'))->__toString());
        $this->assertSame('https', (new Scheme('https'))->__toString());
    }

    /**
     * Test that empty scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\SchemeInvalidArgumentException
     * @expectedExceptionMessage Scheme "" is empty.
     */
    public function testEmptySchemeIsInvalid()
    {
        new Scheme('');
    }

    /**
     * Test that invalid scheme is invalid.
     *
     * @expectedException DataTypes\Exceptions\SchemeInvalidArgumentException
     * @expectedExceptionMessage Scheme "foobar" is invalid: Scheme must be "http" or "https"
     */
    public function testInvalidSchemeIsInvalid()
    {
        new Scheme('foobar');
    }
}
