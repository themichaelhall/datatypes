<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Common\ParsableDataTypeInterface;

/**
 * Interface for Host class.
 *
 * @since 1.0.0
 */
interface HostInterface extends ParsableDataTypeInterface
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
    public function equals(self $host): bool;

    /**
     * Returns the hostname of the host.
     *
     * @since 1.0.0
     *
     * @return HostnameInterface The hostname of the host.
     */
    public function getHostname(): HostnameInterface;

    /**
     * Returns the IP address of the host or null if the host has no IP address.
     *
     * @since 1.0.0
     *
     * @return IPAddressInterface|null The IP address of the host or null if the host has no IP address.
     */
    public function getIPAddress(): ?IPAddressInterface;
}
