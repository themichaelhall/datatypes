<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
declare(strict_types=1);

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
     * Returns true if the IP address equals other IP address, false otherwise.
     *
     * @since 1.2.0
     *
     * @param IPAddressInterface $ipAddress The other IP address.
     *
     * @return bool True if the IP address equals other IP address, false otherwise.
     */
    public function equals(IPAddressInterface $ipAddress): bool
    {
        return $this->getParts() === $ipAddress->getParts();
    }

    /**
     * Returns the IP address parts.
     *
     * @since 1.0.0
     *
     * @return int[] The IP address parts.
     */
    public function getParts(): array
    {
        return $this->myOctets;
    }

    /**
     * Returns the IP address as an integer.
     *
     * @since 1.2.0
     *
     * @return int The IP address as an integer.
     */
    public function toInteger(): int
    {
        return ($this->myOctets[0] << 24) + ($this->myOctets[1] << 16) + ($this->myOctets[2] << 8) + $this->myOctets[3];
    }

    /**
     * Returns a copy of the IP address instance masked with the specified mask.
     *
     * @since 1.0.0
     *
     * @param IPAddressInterface $mask The mask.
     *
     * @return IPAddressInterface The IP address instance.
     */
    public function withMask(IPAddressInterface $mask): IPAddressInterface
    {
        $octets = $this->getParts();
        $maskOctets = $mask->getParts();

        for ($i = 0; $i < 4; $i++) {
            $octets[$i] = $octets[$i] & $maskOctets[$i];
        }

        return new self($octets);
    }

    /**
     * Returns the IP address as a string.
     *
     * @since 1.0.0
     *
     * @return string The IP address as a string.
     */
    public function __toString(): string
    {
        return implode('.', $this->myOctets);
    }

    /**
     * Creates an IP address from an integer.
     *
     * @since 1.2.0
     *
     * @param int $i The integer.
     *
     * @return IPAddressInterface The IP address instance.
     */
    public static function fromInteger(int $i): IPAddressInterface
    {
        return new self([
            ($i >> 24) & 0xFF,
            ($i >> 16) & 0xFF,
            ($i >> 8) & 0xFF,
            $i & 0xFF,
        ]);
    }

    /**
     * Creates an IP address from octets.
     *
     * @since 1.0.0
     *
     * @param int[] $octets The octets.
     *
     * @throws \InvalidArgumentException         If the octets parameter is not an array of integers.
     * @throws IPAddressInvalidArgumentException If the $octets parameter is not a valid array of octets.
     *
     * @return IPAddressInterface The IP address.
     */
    public static function fromParts(array $octets): IPAddressInterface
    {
        if (!self::myValidateOctets($octets, $error)) {
            throw new IPAddressInvalidArgumentException('Octets are invalid: ' . $error);
        }

        return new self($octets);
    }

    /**
     * Checks if an IP address is valid.
     *
     * @since 1.0.0
     *
     * @param string $ipAddress The IP address.
     *
     * @return bool True if the $ipAddress parameter is a valid IP address, false otherwise.
     */
    public static function isValid(string $ipAddress): bool
    {
        return self::myParse($ipAddress);
    }

    /**
     * Parses an IP address.
     *
     * @since 1.0.0
     *
     * @param string $ipAddress The IP address.
     *
     * @throws IPAddressInvalidArgumentException If the $ipAddress parameter is not a valid IP address.
     *
     * @return IPAddressInterface The IPAddress instance.
     */
    public static function parse(string $ipAddress): IPAddressInterface
    {
        if (!self::myParse($ipAddress, $octets, $error)) {
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
     * @return IPAddressInterface|null The IPAddress instance if the $ipAddress parameter is a valid IP address, null otherwise.
     */
    public static function tryParse(string $ipAddress): ?IPAddressInterface
    {
        if (!self::myParse($ipAddress, $octets)) {
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
     * @param string      $ipAddress The IP address.
     * @param int[]|null  $octets    The octets if parsing was successful, undefined otherwise.
     * @param string|null $error     The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse(string $ipAddress, ?array &$octets = null, ?string &$error = null): bool
    {
        if ($ipAddress === '') {
            $error = 'IP address "' . $ipAddress . '" is empty.';

            return false;
        }

        $ipAddressParts = explode('.', $ipAddress);

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
            $octets[] = $octet;
        }

        return true;
    }

    /**
     * Validates an IP address part.
     *
     * @param string      $ipAddressPart The IP address part.
     * @param int|null    $octet         The resulting octet.
     * @param string|null $error         The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myValidateIpAddressPart(string $ipAddressPart, ?int &$octet, ?string &$error): bool
    {
        if ($ipAddressPart === '') {
            $error = 'Octet "' . $ipAddressPart . '" is empty.';

            return false;
        }

        if (preg_match('/[^0-9]/', $ipAddressPart, $matches)) {
            $error = 'Octet "' . $ipAddressPart . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        $octet = intval($ipAddressPart);

        if (!self::myValidateOctet($octet, $error)) {
            return false;
        }

        return true;
    }

    /**
     * Validates an array of octets.
     *
     * @param int[]       $octets The array of octets.
     * @param string|null $error  The error text if validation was not successful, undefined otherwise.
     *
     * @throws \InvalidArgumentException If the octets parameter is not an array of integers.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myValidateOctets(array $octets, ?string &$error): bool
    {
        if (count($octets) !== 4) {
            $error = 'IP address must consist of four octets.';

            return false;
        }

        foreach ($octets as $octet) {
            if (!is_int($octet)) {
                throw new \InvalidArgumentException('$octets is not an array of integers.');
            }

            if (!self::myValidateOctet($octet, $error)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates an octet.
     *
     * @param int         $octet The octet.
     * @param string|null $error The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myValidateOctet(int $octet, ?string &$error): bool
    {
        if ($octet < 0) {
            $error = 'Octet ' . $octet . ' is out of range: Minimum value for an octet is 0.';

            return false;
        }

        if ($octet > 255) {
            $error = 'Octet ' . $octet . ' is out of range: Maximum value for an octet is 255.';

            return false;
        }

        return true;
    }

    /**
     * @var int[] My octets.
     */
    private $myOctets;
}
