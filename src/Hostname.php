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
        // Empty hostname is invalid.
        if ($hostname === '') {
            throw new \InvalidArgumentException('Hostname "' . $hostname . '" is empty');
        }

        // Split hostname and validate individual parts.
        $this->_parts = [];
        $parts = explode(
            '.',
            substr($hostname, -1) === '.' ? substr($hostname, 0, -1) : $hostname // Remove trailing "." from hostname if present.
        );

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

        // Part can not be empty.
        if ($part === '') {
            throw new \InvalidArgumentException('Hostname "' . $hostname . '" is invalid. Part of hostname "' . $part . '" is empty.');
        }

        return $part;
    }

    /**
     * @var string[] My hostname parts.
     */
    private $_parts;
}
