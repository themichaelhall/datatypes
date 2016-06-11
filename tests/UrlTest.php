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
        $this->assertSame('http://www.domain.com/', (new Url('http://www.domain.com/'))->__toString());
    }
}
