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
 * Interface for Hostname class.
 *
 * @since 1.0.0
 */
interface HostnameInterface extends ParsableDataTypeInterface
{
    /**
     * Returns true if the hostname equals other hostname, false otherwise.
     *
     * @since 1.2.0
     *
     * @param HostnameInterface $hostname The other hostname.
     *
     * @return bool True if the hostname equals other hostname, false otherwise.
     */
    public function equals(self $hostname): bool;

    /**
     * Returns the domain name including top-level domain.
     *
     * @since 1.0.0
     *
     * @return string The domain name including top-level domain.
     */
    public function getDomainName(): string;

    /**
     * Returns the domain parts.
     *
     * @since 1.0.0
     *
     * @return string[] The domain parts.
     */
    public function getDomainParts(): array;

    /**
     * Returns the top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     *
     * @since 1.0.0
     *
     * @return string|null The top-level domain of the hostname if hostname has a top-level domain, null otherwise.
     */
    public function getTld(): ?string;

    /**
     * Returns a copy of the Hostname instance with the specified top-level domain.
     *
     * @since 1.0.0
     *
     * @param string $tld The top-level domain.
     *
     * @return HostnameInterface The Hostname instance.
     */
    public function withTld(string $tld): self;
}
