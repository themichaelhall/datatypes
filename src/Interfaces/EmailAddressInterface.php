<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
declare(strict_types=1);

namespace DataTypes\Interfaces;

/**
 * Interface for EmailAddress class.
 *
 * @since 1.1.0
 */
interface EmailAddressInterface extends DataTypeInterface
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
    public function equals(self $emailAddress);

    /**
     * Returns the host of the email address.
     *
     * @since 1.1.0
     *
     * @return HostInterface The host of the email address.
     */
    public function getHost();

    /**
     * Returns the username of the email address.
     *
     * @since 1.1.0
     *
     * @return string The username of the email address.
     */
    public function getUsername();

    /**
     * Returns a copy of the email address instance with the specified host.
     *
     * @since 1.1.0
     *
     * @param HostInterface $host The host.
     *
     * @return EmailAddressInterface The email address instance.
     */
    public function withHost(HostInterface $host);

    /**
     * Returns a copy of the email address instance with the specified username.
     *
     * @since 1.1.0
     *
     * @param string $username The username.
     *
     * @return EmailAddressInterface The email address instance.
     */
    public function withUsername($username);
}
