<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */
namespace DataTypes\Interfaces;

/**
 * Interface for Hostname class.
 *
 * @since 1.0.0
 */
interface HostnameInterface extends DataTypeInterface
{
    /**
     * Returns the domain name including top-level domain.
     *
     * @since 1.0.0
     *
     * @return string The domain name including top-level domain.
     */
    public function getDomainName();

    /**
     * Returns the domain parts.
     *
     * @since 1.0.0
     *
     * @return string[] The domain parts.
     */
    public function getDomainParts();

    /**
     * Returns the top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     *
     * @since 1.0.0
     *
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld();

    /**
     * Returns a copy of the Hostname instance with the specified top-level domain.
     *
     * @since 1.0.0
     *
     * @param string $tld The top-level domain.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public function withTld($tld);
}
