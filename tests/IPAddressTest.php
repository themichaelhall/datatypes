<?php

use DataTypes\IPAddress;

/**
 * Test IPAdress class.
 */
class IPAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __toString method.
     */
    public function testToString()
    {
        $this->assertSame('127.0.0.1', (new IPAddress('127.0.0.1'))->__toString());
        $this->assertSame('255.255.255.255', (new IPAddress('255.255.255.255'))->__toString());
    }
}
