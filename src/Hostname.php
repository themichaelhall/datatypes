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
        assert(is_string($hostname), '$hostname is not a string');

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
        // Remove trailing dot if present in hostname.
        if (substr($hostname, -1) == '.') {
            $hostname = substr($hostname, 0, -1);
        }

        // Split hostname and validate individual parts.
        $this->_parts = [];
        $parts = explode('.', $hostname);
        foreach ($parts as $part) {
            $this->_parts[] = static::_normalizeAndValidatePart($part, $hostname);
        }
    }

    /**
     * Normalizes and validates a part of the hostname.
     * @param string $part The part of the hostname.
     * @param string $hostname The hostname.
     * @return string The normalized part.
     */
    private static function _normalizeAndValidatePart($part, $hostname)
    {
        $part = strtolower($part);
        // fixme: validate part
        return $part;
    }

    /**
     * @var string[] My hostname parts.
     */
    private $_parts;
}
