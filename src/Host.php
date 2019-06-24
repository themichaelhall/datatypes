<?php
/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */
declare(strict_types=1);

namespace DataTypes;

use DataTypes\Exceptions\HostInvalidArgumentException;
use DataTypes\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Interfaces\HostInterface;
use DataTypes\Interfaces\HostnameInterface;
use DataTypes\Interfaces\IPAddressInterface;

/**
 * Class representing a host.
 *
 * @since 1.0.0
 */
class Host implements HostInterface
{
    /**
     * Returns true if the host equals other host, false otherwise.
     *
     * @since 1.2.0
     *
     * @param HostInterface $host The other host.
     *
     * @return bool True if the host equals other host, false otherwise.
     */
    public function equals(HostInterface $host): bool
    {
        if ($this->getIPAddress() !== null && $host->getIPAddress() !== null) {
            return $this->getIPAddress()->equals($host->getIPAddress());
        }

        return $this->getHostname()->equals($host->getHostname());
    }

    /**
     * Returns the hostname of the host.
     *
     * @since 1.0.0
     *
     * @return HostnameInterface The hostname of the host.
     */
    public function getHostname(): HostnameInterface
    {
        if ($this->hostname !== null) {
            return $this->hostname;
        }

        // If no hostname is present, create a standard "in-addr.arpa" hostname from IP address.
        $ipAddressParts = $this->ipAddress->getParts();

        return Hostname::fromParts([
            strval($ipAddressParts[3]),
            strval($ipAddressParts[2]),
            strval($ipAddressParts[1]),
            strval($ipAddressParts[0]),
            'in-addr',
        ], 'arpa');
    }

    /**
     * Returns The IP address of the host or null if the host has no IP address.
     *
     * @since 1.0.0
     *
     * @return IPAddressInterface|null The IP address of the host or null if the host has no IP address.
     */
    public function getIPAddress(): ?IPAddressInterface
    {
        return $this->ipAddress;
    }

    /**
     * Returns the host as a string.
     *
     * @since 1.0.0
     *
     * @return string The host as a string.
     */
    public function __toString(): string
    {
        if ($this->ipAddress !== null) {
            return $this->ipAddress->__toString();
        }

        return $this->hostname->__toString();
    }

    /**
     * Creates a host from a hostname.
     *
     * @since 1.0.0
     *
     * @param HostnameInterface $hostname The hostname.
     *
     * @return HostInterface The host.
     */
    public static function fromHostname(HostnameInterface $hostname): HostInterface
    {
        return new self($hostname, null);
    }

    /**
     * Creates a host from an IP address.
     *
     * @since 1.0.0
     *
     * @param IPAddressInterface $ipAddress
     *
     * @return HostInterface The host.
     */
    public static function fromIPAddress(IPAddressInterface $ipAddress): HostInterface
    {
        return new self(null, $ipAddress);
    }

    /**
     * Checks if a host is valid.
     *
     * @since 1.0.0
     *
     * @param string $host The host.
     *
     * @return bool True if the $host parameter is a valid host, false otherwise.
     */
    public static function isValid(string $host): bool
    {
        return self::doParse($host);
    }

    /**
     * Parses a host.
     *
     * @since 1.0.0
     *
     * @param string $host The host.
     *
     * @throws HostInvalidArgumentException If the $host parameter is not a valid host.
     *
     * @return HostInterface The Host instance.
     */
    public static function parse(string $host): HostInterface
    {
        if (!self::doParse($host, $hostname, $ipAddress, $error)) {
            throw new HostInvalidArgumentException($error);
        }

        return new self($hostname, $ipAddress);
    }

    /**
     * Parses a host.
     *
     * @since 1.0.0
     *
     * @param string $host The host.
     *
     * @return HostInterface|null The Host instance if the $host parameter is a valid host, null otherwise.
     */
    public static function tryParse(string $host): ?HostInterface
    {
        if (!self::doParse($host, $hostname, $ipAddress)) {
            return null;
        }

        return new self($hostname, $ipAddress);
    }

    /**
     * Constructs a host from either a hostname or an IP address.
     *
     * @param HostnameInterface|null  $hostname  The hostname.
     * @param IPAddressInterface|null $ipAddress The IP address.
     */
    private function __construct(?HostnameInterface $hostname = null, ?IPAddressInterface $ipAddress = null)
    {
        $this->hostname = $hostname;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Tries to parse a host and returns the result or error text.
     *
     * @param string                  $host      The host.
     * @param HostnameInterface|null  $hostname  The hostname or null if parsing was successful, undefined otherwise.
     * @param IPAddressInterface|null $ipAddress The IP address or null if parsing was successful, undefined otherwise.
     * @param string|null             $error     The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function doParse(string $host, ?HostnameInterface &$hostname = null, ?IPAddressInterface &$ipAddress = null, ?string &$error = null): bool
    {
        if ($host === '') {
            $error = 'Host "' . $host . '" is empty.';

            return false;
        }

        // Parse host as either a hostname or an IP address.
        try {
            $hostname = Hostname::parse($host);
        } catch (HostnameInvalidArgumentException $e) {
            $error = 'Host "' . $host . '" is invalid: ' . $e->getMessage();

            $ipAddress = IPAddress::tryParse($host);
            if ($ipAddress === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * @var HostnameInterface My hostname.
     */
    private $hostname;

    /**
     * @var IPAddressInterface My IP address.
     */
    private $ipAddress;
}
