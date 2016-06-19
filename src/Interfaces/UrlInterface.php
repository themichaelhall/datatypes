<?php

namespace DataTypes\Interfaces;

/**
 * Interface for Url class.
 */
interface UrlInterface extends DataTypeInterface
{
    /**
     * @return HostInterface The host of the url.
     */
    public function getHost();

    /**
     * @return SchemeInterface The scheme of the url.
     */
    public function getScheme();

    /**
     * Returns a copy of the Url instance with the specified host.
     *
     * @param HostInterface $host The host.
     *
     * @return UrlInterface The Url instance.
     */
    public function withHost(HostInterface $host);

    /**
     * Returns a copy of the Url instance with the specified scheme.
     *
     * @param SchemeInterface $scheme The scheme.
     *
     * @return UrlInterface The Url instance.
     */
    public function withScheme(SchemeInterface $scheme);
}
