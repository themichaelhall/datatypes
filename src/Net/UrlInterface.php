<?php

/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */

declare(strict_types=1);

namespace DataTypes\Net;

use DataTypes\Common\DataTypeInterface;

/**
 * Interface for Url class.
 *
 * @since 1.0.0
 */
interface UrlInterface extends DataTypeInterface
{
    /**
     * Returns true if the url equals other url, false otherwise.
     *
     * @since 1.2.0
     *
     * @param UrlInterface $url The other url.
     *
     * @return bool True if the url equals other url, false otherwise.
     */
    public function equals(self $url): bool;

    /**
     * Returns the fragment of the url or null if the url has no fragment.
     *
     * @since 1.0.0
     *
     * @return string|null The fragment of the url or null if the url has no fragment.
     */
    public function getFragment(): ?string;

    /**
     * Returns the host of the url.
     *
     * @since 1.0.0
     *
     * @return HostInterface The host of the url.
     */
    public function getHost(): HostInterface;

    /**
     * Returns the host and port of the url as a string.
     *
     * @since 1.0.0
     *
     * @return string The host and port of the url.
     */
    public function getHostAndPort(): string;

    /**
     * Returns the path of the url.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The path of the url.
     */
    public function getPath(): UrlPathInterface;

    /**
     * Returns the path and query string of the url as a string.
     *
     * @since 2.1.0
     *
     * @return string the path and query string of the url.
     */
    public function getPathAndQueryString(): string;

    /**
     * Returns the port of the url.
     *
     * @since 1.0.0
     *
     * @return int The port of the url.
     */
    public function getPort(): int;

    /**
     * Returns the query string of the url or null if the url has no query string.
     *
     * @since 1.0.0
     *
     * @return string|null The query string of the url or null if the url has no query string.
     */
    public function getQueryString(): ?string;

    /**
     * Returns the scheme of the url.
     *
     * @since 1.0.0
     *
     * @return SchemeInterface The scheme of the url.
     */
    public function getScheme(): SchemeInterface;

    /**
     * Returns a copy of the Url instance with the specified fragment.
     *
     * @since 1.0.0
     *
     * @param string|null $fragment The fragment or null for no fragment.
     *
     * @return UrlInterface The url instance.
     */
    public function withFragment(?string $fragment = null): self;

    /**
     * Returns a copy of the Url instance with the specified host.
     *
     * @since 1.0.0
     *
     * @param HostInterface $host The host.
     *
     * @return UrlInterface The Url instance.
     */
    public function withHost(HostInterface $host): self;

    /**
     * Returns a copy of the Url instance with the specified path.
     *
     * @since 1.0.0
     *
     * @param UrlPathInterface $path The path.
     *
     * @return UrlInterface The Url instance.
     */
    public function withPath(UrlPathInterface $path): self;

    /**
     * Returns a copy of the Url instance with the specified port.
     *
     * @since 1.0.0
     *
     * @param int $port The port.
     *
     * @return UrlInterface The Url instance.
     */
    public function withPort(int $port): self;

    /**
     * Returns a copy of the Url instance with the specified query string.
     *
     * @since 1.0.0
     *
     * @param string|null $queryString The query string or null for no query string.
     *
     * @return UrlInterface The url instance.
     */
    public function withQueryString(?string $queryString = null): self;

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
    public function withScheme(SchemeInterface $scheme, bool $keepDefaultPort = true): self;
}
