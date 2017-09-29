<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes;

use DataTypes\Exceptions\EmailAddressInvalidArgumentException;
use DataTypes\Exceptions\HostnameInvalidArgumentException;
use DataTypes\Interfaces\EmailAddressInterface;
use DataTypes\Interfaces\HostInterface;

/**
 * Class representing an email address.
 *
 * @since 1.1.0
 */
class EmailAddress implements EmailAddressInterface
{
    /**
     * Returns the host of the email address.
     *
     * @since 1.1.0
     *
     * @return HostInterface The host of the email address.
     */
    public function getHost()
    {
        return $this->myHost;
    }

    /**
     * Returns the username of the email address.
     *
     * @since 1.1.0
     *
     * @return string The username of the email address.
     */
    public function getUsername()
    {
        return $this->myUsername;
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
    public function withHost(HostInterface $host)
    {
        return new self($this->myUsername, $host);
    }

    /**
     * Returns a copy of the email address instance with the specified username.
     *
     * @since 1.1.0
     *
     * @param string $username The username.
     *
     * @throws \InvalidArgumentException            If the $username parameter is not a string.
     * @throws EmailAddressInvalidArgumentException If the $username parameter is not a valid username.
     *
     * @return EmailAddressInterface The email address instance.
     */
    public function withUsername($username)
    {
        if (!is_string($username)) {
            throw new \InvalidArgumentException('$username parameter is not a string.');
        }

        if (!self::myValidateUsername($username, $error)) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return new self($username, $this->myHost);
    }

    /**
     * Returns the email address as a string.
     *
     * @since 1.1.0
     *
     * @return string The email address as a string.
     */
    public function __toString()
    {
        return $this->myUsername . '@' . ($this->myHost->getIPAddress() !== null ? '[' . $this->myHost->__toString() . ']' : $this->myHost->__toString());
    }

    /**
     * Creates an email address from parts.
     *
     * @since 1.1.0
     *
     * @param string        $username The username.
     * @param HostInterface $host     The host.
     *
     * @throws \InvalidArgumentException            If the $username parameter is not a string.
     * @throws EmailAddressInvalidArgumentException If the $username parameter is not a valid username.
     *
     * @return EmailAddress The email address.
     */
    public static function fromParts($username, HostInterface $host)
    {
        if (!is_string($username)) {
            throw new \InvalidArgumentException('$username parameter is not a string.');
        }

        if (!self::myValidateUsername($username, $error)) {
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
     * @throws \InvalidArgumentException If the $emailAddress parameter is not a string.
     *
     * @return bool True if the $emailAddress parameter is a valid email address, false otherwise.
     */
    public static function isValid($emailAddress)
    {
        return self::myParse($emailAddress, true);
    }

    /**
     * Parses an email address.
     *
     * @since 1.1.0
     *
     * @param string $emailAddress The email address.
     *
     * @throws \InvalidArgumentException            If the $emailAddress parameter is not a string.
     * @throws EmailAddressInvalidArgumentException If the $emailAddress parameter is not a valid email address.
     *
     * @return EmailAddressInterface The EmailAddress instance.
     */
    public static function parse($emailAddress)
    {
        if (!self::myParse($emailAddress, false, $username, $host, $error)) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return new self($username, $host);
    }

    /**
     * Parses an email address.
     *
     * @since 1.1.0
     *
     * @param string|null $emailAddress The email address.
     *
     * @throws \InvalidArgumentException If the $emailAddress parameter is not a string.
     *
     * @return EmailAddressInterface The EmailAddress instance if the $emailAddress parameter is a valid email address, false otherwise.
     */
    public static function tryParse($emailAddress)
    {
        if (!self::myParse($emailAddress, false, $username, $host)) {
            return null;
        }

        return new self($username, $host);
    }

    /**
     * Constructs an email address from a username and a host.
     *
     * @param string        $username The username.
     * @param HostInterface $host     The host.
     */
    private function __construct($username, HostInterface $host)
    {
        $this->myUsername = $username;
        $this->myHost = $host;
    }

    /**
     * Tries to parse an email address and returns the result or error text.
     *
     * @param string             $emailAddress The email address.
     * @param bool               $validateOnly If true only validation is performed, if false parse results are returned.
     * @param string|null        $username     The username if parsing was successful, undefined otherwise.
     * @param HostInterface|null $host         The host if parsing was successful, undefined otherwise.
     * @param string|null        $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @throws \InvalidArgumentException If the $emailAddress parameter is not a string.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($emailAddress, $validateOnly, &$username = null, HostInterface &$host = null, &$error = null)
    {
        if (!is_string($emailAddress)) {
            throw new \InvalidArgumentException('$emailAddress parameter is not a string.');
        }

        if ($emailAddress === '') {
            $error = 'Email address "" is empty.';

            return false;
        }

        $parts = explode('@', $emailAddress, 2);
        if (count($parts) < 2) {
            $error = 'Email address "' . $emailAddress . '" is invalid: Character "@" is missing.';

            return false;
        }

        $username = $parts[0];
        $hostname = $parts[1];

        if ($validateOnly) {
            return self::myValidateUsername($username) && Hostname::isValid($hostname);
        }

        if (!self::myValidateUsername($username, $error)) {
            $error = 'Email address "' . $emailAddress . '" is invalid: ' . $error;

            return false;
        }

        try {
            $host = Host::fromHostname(Hostname::parse($hostname));
        } catch (HostnameInvalidArgumentException $exception) {
            $error = 'Email address "' . $emailAddress . '" is invalid: ' . $exception->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Validates the username.
     *
     * @param string      $username The username.
     * @param string|null $error    The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function myValidateUsername($username, &$error = null)
    {
        if ($username === '') {
            $error = 'Username "" is empty.';

            return false;
        }

        if (strpos($username, '..') !== false) {
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
    private $myUsername;

    /**
     * @var HostInterface My host.
     */
    private $myHost;
}
