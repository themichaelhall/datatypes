<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Common\DataTypeInterface;

/**
 * Interface for IPAddress class.
 *
 * @since 1.0.0
 */
interface IPAddressInterface extends DataTypeInterface
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
    public function equals(self $ipAddress): bool;

    /**
     * Returns the IP address parts.
     *
     * @since 1.0.0
     *
     * @return int[] The IP address parts.
     */
    public function getParts(): array;

    /**
     * Returns the IP address as an integer.
     *
     * @since 1.2.0
     *
     * @return int The IP address as an integer.
     */
    public function toInteger(): int;

    /**
     * Returns a copy of the IP address masked with the specified mask.
     *
     * @since 1.0.0
     *
     * @param IPAddressInterface $mask The mask.
     *
     * @return IPAddressInterface The IP address.
     */
    public function withMask(self $mask): self;
}
