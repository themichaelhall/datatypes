<?php

namespace DataTypes;

use DataTypes\Interfaces\IPAddressInterface;

/**
 * Class representing an IP address.
 */
class IPAddress implements IPAddressInterface
{
    /**
     * Constructs an IP address.
     *
     * @param string $ipAddress The IP address.
     */
    public function __construct($ipAddress)
    {
        assert(is_string($ipAddress), '$ipAddress is not a string');

        $this->_value = $ipAddress;
    }

    /**
     * @return string The IP address as a string.
     */
    public function __toString()
    {
        return $this->_value;
    }

    /**
     * @var string My value.
     */
    private $_value;
}
