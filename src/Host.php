<?php

namespace DataTypes;

use DataTypes\Exceptions\HostInvalidArgumentException;
use DataTypes\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Interfaces\HostInterface;

/**
 * Class representing a host.
 */
class Host implements HostInterface
{
    /**
     * @return string The host as a string.
     */
    public function __toString()
    {
        return $this->myHost;
    }

    // fixme: fromHostname()
    // fixme: fromIPAddress()

    /**
     * Checks if a host is valid.
     *
     * @param string $host The host.
     *
     * @return bool True if the $host parameter is a valid host, false otherwise.
     */
    public static function isValid($host)
    {
        assert(is_string($host), '$host is not a string');

        return static::myParse($host, true);
    }

    /**
     * Parses a host.
     *
     * @param string $host The host.
     *
     * @throws HostInvalidArgumentException If the $host parameter is not a valid host.
     *
     * @return HostInterface The Host instance.
     */
    public static function parse($host)
    {
        assert(is_string($host), '$host is not a string');

        if (!static::myParse($host, false, $error)) {
            throw new HostInvalidArgumentException($error);
        }

        return new self($host);
    }

    // fixme: toHostname()
    // fixme: toIPAddress()

    /**
     * Parses a host.
     *
     * @param string $host The host.
     *
     * @return HostInterface The Host instance if the $host parameter is a valid host, null otherwise.
     */
    public static function tryParse($host)
    {
        assert(is_string($host), '$host is not a string');

        if (!static::myParse($host, false)) {
            return null;
        }

        return new self($host);
    }

    /**
     * Constructs a host from host.
     *
     * @param string $host The host.
     */
    private function __construct($host)
    {
        $this->myHost = $host;
    }

    /**
     * Tries to parse a host and returns the result or error text.
     *
     * @param string      $host         The host.
     * @param bool        $validateOnly If true only validation is performed, if false parse results are returned.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($host, $validateOnly, &$error = null)
    {
        // Pre-validate host.
        if (!static::myPreValidate($host, $error)) {
            return false;
        }

        // Validate only?
        if ($validateOnly) {
            return Hostname::isValid($host) || IPAddress::isValid($host);
        }

        // Parse host as either a hostname or an IP address.
        try {
            Hostname::parse($host);
        } catch (HostnameInvalidArgumentException $e) {
            $error = 'Host "' . $host . '" is invalid: ' . $e->getMessage();

            return IPAddress::isValid($host);
        }

        return true;
    }

    /**
     * Pre-validates a host.
     *
     * @param string $host  The host.
     * @param string $error The error text if pre-validation was not successful, undefined otherwise.
     *
     * @return bool True if pre-validation was successful, false otherwise.
     */
    private static function myPreValidate($host, &$error)
    {
        // Empty host is invalid.
        if ($host === '') {
            $error = 'Host "' . $host . '" is empty.';

            return false;
        }

        return true;
    }

    /**
     * @var string My host.
     */
    private $myHost;
}
