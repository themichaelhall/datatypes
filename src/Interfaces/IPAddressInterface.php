<?php

namespace DataTypes\Interfaces;

/**
 * Interface for IPAddress class.
 */
interface IPAddressInterface extends DataTypeInterface
{
    /**
     * @return int[] The IP address parts.
     */
    public function getParts();
}
