<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes;

use DataTypes\Exceptions\IPAddressInvalidArgumentException;
use DataTypes\Interfaces\IPAddressInterface;

/**
 * Class representing an IP address.
 *
 * @since 1.0.0
 */
class IPAddress implements IPAddressInterface
{
    /**
     * Returns the IP address parts.
     *
     * @since 1.0.0
     *
     * @return int[] The IP address parts.
     */
    public function getParts()
    {
        return $this->myOctets;
    }

    /**
     * Returns the IP address as a string.
     *
     * @since 1.0.0
     *
     * @return string The IP address as a string.
     */
    public function __toString()
    {
        return implode('.', $this->myOctets);
    }

    /**
     * Checks if an IP address is valid.
     *
     * @since 1.0.0
     *
     * @param string $ipAddress The IP address.
     *
     * @throws \InvalidArgumentException If the $ipAddress parameter is not a string.
     *
     * @return bool True if the $ipAddress parameter is a valid IP address, false otherwise.
     */
    public static function isValid($ipAddress)
    {
        if (!is_string($ipAddress)) {
            throw new \InvalidArgumentException('$ipAddress parameter is not a string.');
        }

        return self::myParse($ipAddress, true);
    }

    /**
     * Parses an IP address.
     *
     * @since 1.0.0
     *
     * @param string $ipAddress The IP address.
     *
     * @throws \InvalidArgumentException         If the $ipAddress parameter is not a string.
     * @throws IPAddressInvalidArgumentException If the $ipAddress parameter is not a valid IP address.
     *
     * @return IPAddressInterface The IPAddress instance.
     */
    public static function parse($ipAddress)
    {
        if (!is_string($ipAddress)) {
            throw new \InvalidArgumentException('$ipAddress parameter is not a string.');
        }

        if (!self::myParse($ipAddress, false, $octets, $error)) {
            throw new IPAddressInvalidArgumentException($error);
        }

        return new self($octets);
    }

    /**
     * Parses an IP address.
     *
     * @since 1.0.0
     *
     * @param string $ipAddress The IP address.
     *
     * @throws \InvalidArgumentException If the $ipAddress parameter is not a string.
     *
     * @return IPAddressInterface|null The IPAddress instance if the $ipAddress parameter is a valid IP address, null otherwise.
     */
    public static function tryParse($ipAddress)
    {
        if (!is_string($ipAddress)) {
            throw new \InvalidArgumentException('$ipAddress parameter is not a string.');
        }

        if (!self::myParse($ipAddress, false, $octets)) {
            return null;
        }

        return new self($octets);
    }

    /**
     * Constructs an IP address from octets.
     *
     * @param int[] $octets The octets.
     */
    private function __construct(array $octets)
    {
        $this->myOctets = $octets;
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
    private static function myParse($ipAddress, $validateOnly, array &$octets = null, &$error = null)
    {
        // Pre-validate IP address.
        if (!self::myPreValidate($ipAddress, $error)) {
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
            if (!self::myValidateIpAddressPart($ipAddressPart, $octet, $error)) {
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
    private static function myPreValidate($ipAddress, &$error)
    {
        // Empty IP address is invalid.
        if ($ipAddress === '') {
            $error = 'IP address "' . $ipAddress . '" is empty.';

            return false;
        }

        return true;
    }

    /**
     * Validates an IP address part.
     *
     * @param string $ipAddressPart The IP address part.
     * @param int    $octet         The resulting octet.
     * @param string $error         The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myValidateIpAddressPart($ipAddressPart, &$octet, &$error)
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
    private $myOctets;
}
