<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes\Interfaces;

/**
 * Interface for IPAddress class.
 *
 * @since 1.0.0
 */
interface IPAddressInterface extends DataTypeInterface
{
    /**
     * Returns the IP address parts.
     *
     * @since 1.0.0
     *
     * @return int[] The IP address parts.
     */
    public function getParts();

    /**
     * Returns a copy of the IP address masked with the specified mask.
     *
     * @since 1.0.0
     *
     * @param IPAddressInterface $mask The mask.
     *
     * @return IPAddressInterface The IP address.
     */
    public function withMask(self $mask);
}
