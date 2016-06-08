<?php

namespace DataTypes;

use DataTypes\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Interfaces\HostnameInterface;

/**
 * Class representing a hostname.
 */
class Hostname implements HostnameInterface
{
    /**
     * Constructs a hostname.
     *
     * @param string $hostname The hostname as a string.
     *
     * @throws HostnameInvalidArgumentException If the $hostname parameter is not a valid hostname.
     */
    public function __construct($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        if (!static::_parse($hostname, $tld, $domain, $result, $error)) {
            throw new HostnameInvalidArgumentException($error);
        }

        $this->_tld = $tld;
        $this->_domain = $domain;
        $this->_parts = $result;
    }

    /**
     * @return string The domain name including top-level domain.
     */
    public function getDomain()
    {
        if ($this->_tld === null) {
            return $this->_domain;
        }

        return $this->_domain . '.' . $this->_tld;
    }

    /**
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld()
    {
        return $this->_tld;
    }

    /**
     * @return string The hostname as a string.
     */
    public function __toString()
    {
        return implode('.', $this->_parts);
    }

    /**
     * Checks if a hostname is valid.
     *
     * @param string $hostname The hostname.
     *
     * @return bool True if the $hostname parameter is a valid hostname, false otherwise.
     */
    public static function isValid($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        return static::_parse($hostname);
    }

    /**
     * Parses a hostname and returns a Hostname instance.
     *
     * @param string $hostname The hostname as a string.
     *
     * @return Hostname|null The Hostname instance if the $hostname parameter is a valid hostname, null otherwise.
     */
    public static function tryParse($hostname)
    {
        assert(is_string($hostname), '$hostname is not a string');

        try {
            $result = new self($hostname);

            return $result;
        } catch (HostnameInvalidArgumentException $e) {
        }

        return null;
    }

    /**
     * Tries to parse a hostname and returns the result or error text.
     *
     * @param string      $hostname The hostname as a string.
     * @param string|null $tld      The top-level domain if parsing was successful, null otherwise.
     * @param string|null $domain   The domain without top-level domain if parsing was successful, null otherwise.
     * @param array|null  $result   The result if parsing was successful, null otherwise.
     * @param string|null $error    The error text if parsing was successful, null otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($hostname, &$tld = null, &$domain = null, array &$result = null, &$error = null)
    {
        assert(is_string($hostname), '$hostname is not a string');

        $result = null;
        $error = null;

        // Empty hostname is invalid.
        if ($hostname === '') {
            $error = 'Hostname "' . $hostname . '" is empty.';

            return false;
        }

        // Split hostname in parts.
        $parts = explode(
            '.',
            substr($hostname, -1) === '.' ? substr($hostname, 0, -1) : $hostname // Remove trailing "." from hostname.
        );

        // Normalize and validate individual parts.
        $result = [];
        foreach ($parts as $part) {
            $result[] = strtolower($part);

            if ($part === '') {
                $error = 'Hostname "' . $hostname . '" is invalid: Part of hostname "' . $part . '" is empty.';

                return false;
            }
        }

        // Copy the parts into the result.
        if (count($result) == 1) {
            $domain = $result[0];
            $tld = null;
        } else {
            $domain = $result[count($result) - 2];
            $tld = $result[count($result) - 1];
        }

        return true;
    }

    /**
     * @var string[] My hostname parts.
     */
    private $_parts; // fixme: remove this

    /**
     * @var string|null My top-level domain if this hostname has a top-level domain, null otherwise.
     */
    private $_tld;

    /**
     * @var string My domain name, without top-level domain.
     */
    private $_domain;
}
