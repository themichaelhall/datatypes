<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Net\Exceptions\HostInvalidArgumentException;
use DataTypes\Net\Exceptions\HostnameInvalidArgumentException;

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
        if ($this->hostname === null) {
            $ipAddressParts = $this->ipAddress->getParts();

            return Hostname::fromParts([
                strval($ipAddressParts[3]),
                strval($ipAddressParts[2]),
                strval($ipAddressParts[1]),
                strval($ipAddressParts[0]),
                'in-addr',
            ], 'arpa');
        }

        return $this->hostname;
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
        return self::doParse($host) !== null;
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
        $result = self::doParse($host, $error);
        if ($result === null) {
            throw new HostInvalidArgumentException($error);
        }

        return $result;
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
        return self::doParse($host);
    }

    /**
     * Constructs a host from either a hostname or an IP address.
     *
     * @param HostnameInterface|null  $hostname  The hostname.
     * @param IPAddressInterface|null $ipAddress The IP address.
     */
    private function __construct(?HostnameInterface $hostname, ?IPAddressInterface $ipAddress)
    {
        $this->hostname = $hostname;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Tries to parse a host and returns the result or error text.
     *
     * @param string      $str   The host to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The host if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        if ($str === '') {
            $error = 'Host "' . $str . '" is empty.';

            return null;
        }

        $hostname = null;
        $ipAddress = null;

        try {
            $hostname = Hostname::parse($str);
        } catch (HostnameInvalidArgumentException $e) {
            $error = 'Host "' . $str . '" is invalid: ' . $e->getMessage();

            $ipAddress = IPAddress::tryParse($str);
            if ($ipAddress === null) {
                return null;
            }
        }

        return new self($hostname, $ipAddress);
    }

    /**
     * @var HostnameInterface|null The hostname.
     */
    private ?HostnameInterface $hostname;

    /**
     * @var IPAddressInterface|null The IP address.
     */
    private ?IPAddressInterface $ipAddress;
}
