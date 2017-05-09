<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes\Interfaces;

/**
 * Interface for Url class.
 *
 * @since 1.0.0
 */
interface UrlInterface extends DataTypeInterface
{
    /**
     * Returns the host of the url.
     *
     * @since 1.0.0
     *
     * @return HostInterface The host of the url.
     */
    public function getHost();

    /**
     * Returns the host and port of the url as a string.
     *
     * @since 1.0.0
     *
     * @return string The host and port of the url.
     */
    public function getHostAndPort();

    /**
     * Returns the path of the url.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The path of the url.
     */
    public function getPath();

    /**
     * Returns the port of the url.
     *
     * @since 1.0.0
     *
     * @return int The port of the url.
     */
    public function getPort();

    /**
     * Returns the query string of the url or null if the url has no query string.
     *
     * @since 1.0.0
     *
     * @return string|null The query string of the url or null if the url has no query string.
     */
    public function getQueryString();

    /**
     * Returns the scheme of the url.
     *
     * @since 1.0.0
     *
     * @return SchemeInterface The scheme of the url.
     */
    public function getScheme();

    /**
     * Returns a copy of the Url instance with the specified host.
     *
     * @since 1.0.0
     *
     * @param HostInterface $host The host.
     *
     * @return UrlInterface The Url instance.
     */
    public function withHost(HostInterface $host);

    /**
     * Returns a copy of the Url instance with the specified path.
     *
     * @since 1.0.0
     *
     * @param UrlPathInterface $path The path.
     *
     * @return UrlInterface The Url instance.
     */
    public function withPath(UrlPathInterface $path);

    /**
     * Returns a copy of the Url instance with the specified port.
     *
     * @since 1.0.0
     *
     * @param int $port The port.
     *
     * @return UrlInterface The Url instance.
     */
    public function withPort($port);

    /**
     * Returns a copy of the Url instance with the specified query string.
     *
     * @since 1.0.0
     *
     * @param string|null $queryString The query string or null for no query string.
     *
     * @return UrlInterface The url instance.
     */
    public function withQueryString($queryString = null);

    /**
     * Returns a copy of the Url instance with the specified scheme.
     *
     * @since 1.0.0
     *
     * @param SchemeInterface $scheme          The scheme.
     * @param bool            $keepDefaultPort If true, port is changed to the schemes default port if port is current schemes default port, if false port is not changed.
     *
     * @return UrlInterface The Url instance.
     */
    public function withScheme(SchemeInterface $scheme, $keepDefaultPort = true);
}
