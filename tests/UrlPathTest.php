<?php

use DataTypes\UrlPath;

/**
 * Test UrlPath class.
 */
class UrlPathTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('/', UrlPath::parse('/')->__toString());
        $this->assertSame('/foo', UrlPath::parse('/foo')->__toString());
    }
}
