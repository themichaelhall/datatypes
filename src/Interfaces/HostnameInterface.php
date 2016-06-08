<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Hostname class.
 */
interface HostnameInterface extends DataTypeInterface
{
    /**
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld();
}
