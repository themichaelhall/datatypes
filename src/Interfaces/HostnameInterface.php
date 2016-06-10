<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Hostname class.
 */
interface HostnameInterface extends DataTypeInterface
{
    /**
     * @return string The domain name including top-level domain.
     */
    public function getDomain();

    /**
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld();

    /**
     * Returns a copy of the Hostname instance with the specified top-level domain.
     *
     * @param string $tld The top-level domain.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public function withTld($tld);
}
