<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Net\Exceptions\EmailAddressInvalidArgumentException;
use DataTypes\Net\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Net\Exceptions\IPAddressInvalidArgumentException;

/**
 * Class representing an email address.
 *
 * @since 1.1.0
 */
class EmailAddress implements EmailAddressInterface
{
    /**
     * Returns true if the email address equals other email address, false otherwise.
     *
     * @since 1.2.0
     *
     * @param EmailAddressInterface $emailAddress The other email address.
     *
     * @return bool True if the email address equals other email address, false otherwise.
     */
    public function equals(EmailAddressInterface $emailAddress): bool
    {
        return $this->getHost()->equals($emailAddress->getHost()) && $this->getUsername() === $emailAddress->getUsername();
    }

    /**
     * Returns the host of the email address.
     *
     * @since 1.1.0
     *
     * @return HostInterface The host of the email address.
     */
    public function getHost(): HostInterface
    {
        return $this->host;
    }

    /**
     * Returns the username of the email address.
     *
     * @since 1.1.0
     *
     * @return string The username of the email address.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Returns a copy of the email address instance with the specified host.
     *
     * @since 1.1.0
     *
     * @param HostInterface $host The host.
     *
     * @return EmailAddressInterface The email address instance.
     */
    public function withHost(HostInterface $host): EmailAddressInterface
    {
        return new self($this->username, $host);
    }

    /**
     * Returns a copy of the email address instance with the specified username.
     *
     * @since 1.1.0
     *
     * @param string $username The username.
     *
     * @throws EmailAddressInvalidArgumentException If the $username parameter is not a valid username.
     *
     * @return EmailAddressInterface The email address instance.
     */
    public function withUsername(string $username): EmailAddressInterface
    {
        if (!self::validateUsername($username, $error)) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return new self($username, $this->host);
    }

    /**
     * Returns the email address as a string.
     *
     * @since 1.1.0
     *
     * @return string The email address as a string.
     */
    public function __toString(): string
    {
        return $this->username . '@' . ($this->host->getIPAddress() !== null ? '[' . $this->host->__toString() . ']' : $this->host->__toString());
    }

    /**
     * Creates an email address from parts.
     *
     * @since 1.1.0
     *
     * @param string        $username The username.
     * @param HostInterface $host     The host.
     *
     * @throws EmailAddressInvalidArgumentException If the $username parameter is not a valid username.
     *
     * @return EmailAddressInterface The email address.
     */
    public static function fromParts(string $username, HostInterface $host): EmailAddressInterface
    {
        if (!self::validateUsername($username, $error)) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return new self($username, $host);
    }

    /**
     * Checks if an email address is valid.
     *
     * @since 1.1.0
     *
     * @param string $emailAddress The email address.
     *
     * @return bool True if the $emailAddress parameter is a valid email address, false otherwise.
     */
    public static function isValid(string $emailAddress): bool
    {
        return self::doParse($emailAddress) !== null;
    }

    /**
     * Parses an email address.
     *
     * @since 1.1.0
     *
     * @param string $emailAddress The email address.
     *
     * @throws EmailAddressInvalidArgumentException If the $emailAddress parameter is not a valid email address.
     *
     * @return EmailAddressInterface The EmailAddress instance.
     */
    public static function parse(string $emailAddress): EmailAddressInterface
    {
        $result = self::doParse($emailAddress, $error);
        if ($result === null) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return $result;
    }

    /**
     * Parses an email address.
     *
     * @since 1.1.0
     *
     * @param string $emailAddress The email address.
     *
     * @return EmailAddressInterface|null The EmailAddress instance if the $emailAddress parameter is a valid email address, false otherwise.
     */
    public static function tryParse(string $emailAddress): ?EmailAddressInterface
    {
        return self::doParse($emailAddress);
    }

    /**
     * Constructs an email address from a username and a host.
     *
     * @param string        $username The username.
     * @param HostInterface $host     The host.
     */
    private function __construct(string $username, HostInterface $host)
    {
        $this->username = $username;
        $this->host = $host;
    }

    /**
     * Tries to parse an email address and returns the result or error text.
     *
     * @param string      $str   The email address to parse.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return self|null The email address if parsing was successful, null otherwise.
     */
    private static function doParse(string $str, ?string &$error = null): ?self
    {
        if ($str === '') {
            $error = 'Email address "" is empty.';

            return null;
        }

        $parts = explode('@', $str, 2);
        if (count($parts) < 2) {
            $error = 'Email address "' . $str . '" is invalid: Character "@" is missing.';

            return null;
        }

        $username = $parts[0];
        if (!self::validateUsername($username, $error)) {
            $error = 'Email address "' . $str . '" is invalid: ' . $error;

            return null;
        }

        $hostname = $parts[1];
        $host = self::parseHostname($hostname, $error);
        if ($host === null) {
            $error = 'Email address "' . $str . '" is invalid: ' . $error;

            return null;
        }

        return new self($username, $host);
    }

    /**
     * Parses the hostname.
     *
     * @param string      $hostname The hostname to parse.
     * @param string|null $error    The error text if parsing was not successful, undefined otherwise.
     *
     * @return HostInterface|null The host if parsing was successful, null otherwise.
     */
    private static function parseHostname(string $hostname, ?string &$error = null): ?HostInterface
    {
        if (strlen($hostname) > 2 && str_starts_with($hostname, '[') && str_ends_with($hostname, ']')) {
            // Hostname is actually an IP address.
            $ipAddress = substr($hostname, 1, -1);

            try {
                $host = Host::fromIPAddress(IPAddress::parse($ipAddress));
            } catch (IPAddressInvalidArgumentException $exception) {
                $error = $exception->getMessage();

                return null;
            }

            return $host;
        }

        try {
            $host = Host::fromHostname(Hostname::parse($hostname));
        } catch (HostnameInvalidArgumentException $exception) {
            $error = $exception->getMessage();

            return null;
        }

        return $host;
    }

    /**
     * Validates the username.
     *
     * @param string      $username The username.
     * @param string|null $error    The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateUsername(string $username, ?string &$error = null): bool
    {
        if ($username === '') {
            $error = 'Username "" is empty.';

            return false;
        }

        if (str_contains($username, '..')) {
            $error = 'Username "' . $username . '" contains "..".';

            return false;
        }

        if (strlen($username) > 64) {
            $error = 'Username "' . $username . '" is too long: Maximum length is 64.';

            return false;
        }

        if (preg_match('/[^0-9a-zA-Z.!#$%&\'*+\/=?^_`{|}~-]/', $username, $matches)) {
            $error = 'Username "' . $username . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * @var string My username.
     */
    private $username;

    /**
     * @var HostInterface My host.
     */
    private $host;
}
