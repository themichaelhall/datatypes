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
}
