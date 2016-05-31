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
        $this->_parse($hostname);
    }

    /**
     * @return string The hostname as a string.
     */
    public function __toString()
    {
        return implode('.', $this->_parts);
    }

    /**
     * Parses a hostname.
     * @param string $hostname The hostname as a string.
     */
    private function _parse($hostname)
    {
        $this->_parts = explode('.', $hostname);
    }

    /**
     * @var string[] My hostname parts.
     */
    private $_parts;
}
