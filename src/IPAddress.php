<?php

namespace DataTypes;

use DataTypes\Exceptions\IPAddressInvalidArgumentException;
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

        if (!static::_parse($ipAddress, false, $octets, $error)) {
            throw new IPAddressInvalidArgumentException($error);
        }

        $this->_octets = $octets;
    }

    /**
     * @return string The IP address as a string.
     */
    public function __toString()
    {
        return implode('.', $this->_octets);
    }

    /**
     * Tries to parse an IP address and returns the result or error text.
     *
     * @param string      $ipAddress    The IP address.
     * @param bool        $validateOnly If true only validation is performed, if false parse results are returned.
     * @param int[]|null  $octets       The octets if parsing was successful, undefined otherwise.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($ipAddress, $validateOnly, array &$octets = null, &$error = null)
    {
        // Pre-validate IP address.
        if (!static::_preValidate($ipAddress, $error)) {
            return false;
        }

        // Split IP address in parts.
        $ipAddressParts = explode('.', $ipAddress);

        // IP address must have four parts.
        if (count($ipAddressParts) !== 4) {
            $error = 'IP address "' . $ipAddress . '" is invalid: IP address must consist of four octets.';

            return false;
        }

        // Validate the parts.
        $octets = [];
        foreach ($ipAddressParts as $ipAddressPart) {
            if (!static::_validateIpAddressPart($ipAddressPart, $octet, $error)) {
                $error = 'IP address "' . $ipAddress . '" is invalid: ' . $error;

                return false;
            }

            // Save the resulting octet.
            if (!$validateOnly) {
                $octets[] = $octet;
            }
        }

        return true;
    }

    /**
     * Pre-validates a IP address.
     *
     * @param string $ipAddress The IP address.
     * @param string $error     The error text if pre-validation was not successful, undefined otherwise.
     *
     * @return bool True if pre-validation was successful, false otherwise.
     */
    private static function _preValidate($ipAddress, &$error)
    {
        // Empty IP address is invalid.
        if ($ipAddress === '') {
            $error = 'IP address "' . $ipAddress . '" is empty.';

            return false;
        }

        return true;
    }

    /**
     * Validates an IP address part
     *
     * @param string $ipAddressPart The IP address part.
     * @param int    $octet         The resulting octet.
     * @param string $error         The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function _validateIpAddressPart($ipAddressPart, &$octet, &$error)
    {
        // Empty octet is invalid.
        if ($ipAddressPart === '') {
            $error = 'Octet "' . $ipAddressPart . '" is empty.';

            return false;
        }

        // Octet containing invalid character is invalid.
        if (preg_match('/[^0-9]/', $ipAddressPart, $matches)) {
            $error = 'Octet "' . $ipAddressPart . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        $octet = intval($ipAddressPart);

        // Octet out of range is invalid.
        if ($octet > 255) {
            $error = 'Octet "' . $ipAddressPart . '" is out of range: Maximum value for octet is 255.';

            return false;
        }

        return true;
    }

    /**
     * @var int[] My octets.
     */
    private $_octets;
}
