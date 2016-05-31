<?php

namespace MichaelHall\DataTypes;

/**
 * Class representing a hostname.
 * @package MichaelHall\DataTypes
 */
class Hostname
{
    /**
     * Constructs a hostname.
     * @param string $hostname The hostname as a string.
     */
    public function __construct($hostname)
    {
        $this->_hostname = $hostname;
    }

    /**
     * @return string The hostname as a string.
     */
    public function __toString()
    {
        return $this->_hostname;
    }

    /**
     * @var string My hostname as a string.
     */
    private $_hostname;
}
