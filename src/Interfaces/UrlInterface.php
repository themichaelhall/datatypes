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
     * @return UrlPathInterface The path of the url.
     */
    public function getPath();

    /**
     * @return int The port of the url.
     */
    public function getPort();

    /**
     * @return string|null The query string of the url.
     */
    public function getQueryString();

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

    // fixme: withPath()
    // fixme: withPort()
    // fixme: withQueryString()

    /**
     * Returns a copy of the Url instance with the specified scheme.
     *
     * @param SchemeInterface $scheme          The scheme.
     * @param bool            $keepDefaultPort If true, port is changed to the schemes default port if port is current schemes default port, if false port is not changed.
     *
     * @return UrlInterface The Url instance.
     */
    public function withScheme(SchemeInterface $scheme, $keepDefaultPort = true);
}
