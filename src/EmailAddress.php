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
     * Returns the email address as a string.
     *
     * @since 1.1.0
     *
     * @return string The email address as a string.
     */
    public function __toString()
    {
        return $this->myEmailAddress;
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
        if (!self::myParse($emailAddress, false, $host, $error)) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return new self($emailAddress, $host);
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
        if (!self::myParse($emailAddress, false, $host)) {
            return null;
        }

        return new self($emailAddress, $host);
    }

    /**
     * Constructs an email address from a host.
     *
     * @param string        $emailAddress The email address.
     * @param HostInterface $host         The host.
     */
    private function __construct($emailAddress, HostInterface $host)
    {
        $this->myEmailAddress = $emailAddress;
        $this->myHost = $host;
    }

    /**
     * Tries to parse an email address and returns the result or error text.
     *
     * @param string             $emailAddress The email address.
     * @param bool               $validateOnly If true only validation is performed, if false parse results are returned.
     * @param HostInterface|null $host         The host if parsing was successful, undefined otherwise.
     * @param string|null        $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @throws \InvalidArgumentException If the $emailAddress parameter is not a string.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($emailAddress, $validateOnly, HostInterface &$host = null, &$error = null)
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

        if ($validateOnly) {
            return Hostname::isValid($parts[1]);
        }

        // Get host.
        try {
            $host = Host::fromHostname(Hostname::parse($parts[1]));
        } catch (HostnameInvalidArgumentException $exception) {
            $error = 'Email address "' . $emailAddress . '" is invalid: ' . $exception->getMessage();

            return false;
        }

        // fixme: validate user

        return true;
    }

    /**
     * @var string My email address. fixme: remove
     */
    private $myEmailAddress;

    /**
     * @var HostInterface My host.
     */
    private $myHost;
}
