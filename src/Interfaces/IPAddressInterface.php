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
}
