<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes;

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
     * Parses an email address.
     *
     * @since 1.1.0
     *
     * @param string $emailAddress The email address.
     *
     * @return EmailAddressInterface The EmailAddress instance.
     */
    public static function parse($emailAddress)
    {
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
     * @var string My email address.
     */
    private $myEmailAddress;
}
