<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Host class.
 */
interface HostInterface extends DataTypeInterface
{
    /**
     * @return IPAddressInterface|null The IP address of the host or null if the host has no IP address.
     */
    public function getIPAddress();
}
