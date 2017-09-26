<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes;

use DataTypes\Exceptions\EmailAddressInvalidArgumentException;
use DataTypes\Interfaces\EmailAddressInterface;

/**
 * Class representing an email address.
 *
 * @since 1.1.0
 */
class EmailAddress implements EmailAddressInterface
{
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
        return self::myParse($emailAddress);
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
        if (!self::myParse($emailAddress, $error)) {
            throw new EmailAddressInvalidArgumentException($error);
        }

        return new self($emailAddress);
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
        if (!self::myParse($emailAddress, $error)) {
            return null;
        }

        return new self($emailAddress);
    }

    /**
     * Constructs an email address from a string.
     *
     * @param string $emailAddress The email address.
     */
    private function __construct($emailAddress)
    {
        $this->myEmailAddress = $emailAddress;
    }

    /**
     * Tries to parse an email address and returns the result or error text.
     *
     * @param string      $emailAddress The email address.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @throws \InvalidArgumentException If the $emailAddress parameter is not a string.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($emailAddress, &$error = null)
    {
        if (!is_string($emailAddress)) {
            throw new \InvalidArgumentException('$emailAddress parameter is not a string.');
        }

        if ($emailAddress === '') {
            $error = 'Email address "" is empty.';

            return false;
        }

        // fixme: more validation.

        return true;
    }

    /**
     * @var string My email address.
     */
    private $myEmailAddress;
}
