<?php
/**
 * This file is a part of the datatypes package.
 *
 * https://github.com/themichaelhall/datatypes
 */
declare(strict_types=1);

namespace DataTypes;

use DataTypes\Exceptions\HostInvalidArgumentException;
use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\Exceptions\UrlPathInvalidArgumentException;
use DataTypes\Exceptions\UrlPathLogicException;
use DataTypes\Interfaces\HostInterface;
use DataTypes\Interfaces\SchemeInterface;
use DataTypes\Interfaces\UrlInterface;
use DataTypes\Interfaces\UrlPathInterface;

/**
 * Class representing a Url.
 *
 * @since 1.0.0
 */
class Url implements UrlInterface
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
    public function equals(UrlInterface $url): bool
    {
        return $this->getScheme()->equals($url->getScheme()) && $this->getHost()->equals($url->getHost()) && $this->getPort() === $url->getPort() && $this->getPath()->equals($url->getPath()) && $this->getQueryString() === $url->getQueryString() && $this->getFragment() === $url->getFragment();
    }

    /**
     * Returns the fragment of the url or null if the url has no fragment.
     *
     * @since 1.0.0
     *
     * @return string|null The fragment of the url or null if the url has no fragment.
     */
    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    /**
     * Returns the host of the url.
     *
     * @since 1.0.0
     *
     * @return HostInterface The host of the url.
     */
    public function getHost(): HostInterface
    {
        return $this->host;
    }

    /**
     * Returns the host and port of the url as a string.
     *
     * @since 1.0.0
     *
     * @return string The host and port of the url.
     */
    public function getHostAndPort(): string
    {
        if ($this->port !== $this->scheme->getDefaultPort()) {
            return $this->host . ':' . $this->port;
        }

        return $this->host->__toString();
    }

    /**
     * Returns the path of the url.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The path of the url.
     */
    public function getPath(): UrlPathInterface
    {
        return $this->path;
    }

    /**
     * Returns the port of the url.
     *
     * @since 1.0.0
     *
     * @return int The port of the url.
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Returns the query string of the url or null if the url has no query string.
     *
     * @since 1.0.0
     *
     * @return string|null The query string of the url or null if the url has no query string.
     */
    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    /**
     * Returns the scheme of the url.
     *
     * @since 1.0.0
     *
     * @return SchemeInterface The scheme of the url.
     */
    public function getScheme(): SchemeInterface
    {
        return $this->scheme;
    }

    /**
     * Returns a copy of the Url instance with the specified fragment.
     *
     * @since 1.0.0
     *
     * @param string|null $fragment The fragment or null for no fragment.
     *
     * @throws UrlInvalidArgumentException If the fragment parameter is invalid.
     *
     * @return UrlInterface The url instance.
     */
    public function withFragment(?string $fragment = null): UrlInterface
    {
        if (!self::validateFragment($fragment, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($this->scheme, $this->host, $this->port, $this->path, $this->queryString, $fragment);
    }

    /**
     * Returns a copy of the Url instance with the specified host.
     *
     * @since 1.0.0
     *
     * @param HostInterface $host The host.
     *
     * @return UrlInterface The Url instance.
     */
    public function withHost(HostInterface $host): UrlInterface
    {
        return new self($this->scheme, $host, $this->port, $this->path, $this->queryString, $this->fragment);
    }

    /**
     * Returns a copy of the Url instance with the specified port.
     *
     * @since 1.0.0
     *
     * @param int $port The port.
     *
     * @throws UrlInvalidArgumentException If the port is out of range.
     *
     * @return UrlInterface The Url instance.
     */
    public function withPort(int $port): UrlInterface
    {
        if (!self::validatePort($port, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($this->scheme, $this->host, $port, $this->path, $this->queryString, $this->fragment);
    }

    /**
     * Returns a copy of the Url instance with the specified path.
     *
     * @since 1.0.0
     *
     * @param UrlPathInterface $path The path.
     *
     * @return UrlInterface The Url instance.
     */
    public function withPath(UrlPathInterface $path): UrlInterface
    {
        return new self($this->scheme, $this->host, $this->port, $this->path->withUrlPath($path), $this->queryString, $this->fragment);
    }

    /**
     * Returns a copy of the Url instance with the specified query string.
     *
     * @since 1.0.0
     *
     * @param string|null $queryString The query string or null for no query string.
     *
     * @throws UrlInvalidArgumentException If the query parameter is invalid.
     *
     * @return UrlInterface The url instance.
     */
    public function withQueryString(?string $queryString = null): UrlInterface
    {
        if (!self::validateQueryString($queryString, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($this->scheme, $this->host, $this->port, $this->path, $queryString, $this->fragment);
    }

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
    public function withScheme(SchemeInterface $scheme, bool $keepDefaultPort = true): UrlInterface
    {
        return new self($scheme, $this->host, ($keepDefaultPort && $this->port === $this->scheme->getDefaultPort() ? $scheme->getDefaultPort() : $this->port), $this->path, $this->queryString, $this->fragment);
    }

    /**
     * Returns the url as a string.
     *
     * @since 1.0.0
     *
     * @return string The url as a string.
     */
    public function __toString(): string
    {
        return $this->scheme . '://' . $this->getHostAndPort() . $this->path . ($this->queryString !== null ? '?' . $this->queryString : '') . ($this->fragment !== null ? '#' . $this->fragment : '');
    }

    /**
     * Creates a url from url parts.
     *
     * @since 1.0.0
     *
     * @param SchemeInterface       $scheme      The scheme.
     * @param HostInterface         $host        The host.
     * @param int|null              $port        The port or null if default port for the scheme should be used.
     * @param UrlPathInterface|null $urlPath     The url path or null if root path should be used.
     * @param string|null           $queryString The query string or null if no query string should be used.
     * @param string|null           $fragment    The fragment or null if no fragment should be used.
     *
     * @throws UrlInvalidArgumentException If any of the parameters are invalid.
     *
     * @return UrlInterface The url.
     */
    public static function fromParts(SchemeInterface $scheme, HostInterface $host, ?int $port = null, UrlPathInterface $urlPath = null, ?string $queryString = null, ?string $fragment = null)
    {
        // Default values.
        if ($port === null) {
            $port = $scheme->getDefaultPort();
        }

        if ($urlPath === null) {
            $urlPath = UrlPath::parse('/');
        }

        // Validate parts.
        if (!self::validateParts($port, $urlPath, $queryString, $fragment, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($scheme, $host, $port, $urlPath, $queryString, $fragment);
    }

    /**
     * Checks if a url is valid.
     *
     * @since 1.0.0
     *
     * @param string $url The url.
     *
     * @return bool True if the $url parameter is a valid url, false otherwise.
     */
    public static function isValid(string $url): bool
    {
        return self::doParse(null, $url);
    }

    /**
     * Checks if a relative url is valid.
     *
     * @since 1.0.0
     *
     * @param string       $url     The url.
     * @param UrlInterface $baseUrl The base url.
     *
     * @return bool True if the $url parameter is a valid url, false otherwise.
     */
    public static function isValidRelative(string $url, UrlInterface $baseUrl): bool
    {
        try {
            return self::doParse($baseUrl, $url);
        } catch (UrlPathLogicException $exception) {
            return false;
        }
    }

    /**
     * Parses a url.
     *
     * @since 1.0.0
     *
     * @param string $url The url.
     *
     * @throws UrlInvalidArgumentException If the $url parameter is not a valid url.
     *
     * @return UrlInterface The Url instance.
     */
    public static function parse(string $url): UrlInterface
    {
        if (!self::doParse(null, $url, $scheme, $host, $port, $path, $queryString, $fragment, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($scheme, $host, $port, $path, $queryString, $fragment);
    }

    /**
     * Parses a relative url and combines it with a base url.
     *
     * @since 1.0.0
     *
     * @param string       $url     The url
     * @param UrlInterface $baseUrl The base url.
     *
     * @throws UrlInvalidArgumentException If the $url parameter is not a valid relative url.
     *
     * @return UrlInterface The Url instance.
     */
    public static function parseRelative(string $url, UrlInterface $baseUrl): UrlInterface
    {
        if (!self::doParse($baseUrl, $url, $scheme, $host, $port, $path, $queryString, $fragment, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($scheme, $host, $port, $path, $queryString, $fragment);
    }

    /**
     * Parses a url.
     *
     * @since 1.0.0
     *
     * @param string $url The url.
     *
     * @return UrlInterface|null The Url instance if the $url parameter is a valid url, null otherwise.
     */
    public static function tryParse(string $url): ?UrlInterface
    {
        if (!self::doParse(null, $url, $scheme, $host, $port, $path, $queryString, $fragment)) {
            return null;
        }

        return new self($scheme, $host, $port, $path, $queryString, $fragment);
    }

    /**
     * Parses a relative url and combines it with a base url.
     *
     * @since 1.0.0
     *
     * @param string       $url     The url.
     * @param UrlInterface $baseUrl The base url.
     *
     * @return UrlInterface|null The Url instance if the $url parameter is a valid url, null otherwise.
     */
    public static function tryParseRelative(string $url, UrlInterface $baseUrl): ?UrlInterface
    {
        try {
            if (!self::doParse($baseUrl, $url, $scheme, $host, $port, $path, $queryString, $fragment)) {
                return null;
            }
        } catch (UrlPathLogicException $exception) {
            return null;
        }

        return new self($scheme, $host, $port, $path, $queryString, $fragment);
    }

    /**
     * Constructs a Url.
     *
     * @param SchemeInterface  $scheme      The scheme.
     * @param HostInterface    $host        The host.
     * @param int              $port        The port.
     * @param UrlPathInterface $path        The path.
     * @param string|null      $queryString The query string.
     * @param string|null      $fragment    The fragment.
     */
    private function __construct(SchemeInterface $scheme, HostInterface $host, int $port, UrlPathInterface $path, ?string $queryString = null, ?string $fragment = null)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->queryString = $queryString;
        $this->fragment = $fragment;
    }

    /**
     * Tries to parse a url and returns the result or error text.
     *
     * @param UrlInterface|null     $baseUrl     The base url or null if no base url is present.
     * @param string                $url         The url.
     * @param SchemeInterface|null  $scheme      The scheme if parsing was successful, undefined otherwise.
     * @param HostInterface|null    $host        The host if parsing was successful, undefined otherwise.
     * @param int|null              $port        The port if parsing was successful, undefined otherwise.
     * @param UrlPathInterface|null $path        The path if parsing was successful, undefined otherwise.
     * @param string|null           $queryString The query string if parsing was successful, undefined otherwise.
     * @param string|null           $fragment    The fragment if parsing was successful, undefined otherwise.
     * @param string|null           $error       The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function doParse(?UrlInterface $baseUrl, string $url, ?SchemeInterface &$scheme = null, ?HostInterface &$host = null, ?int &$port = null, ?UrlPathInterface &$path = null, ?string &$queryString = null, ?string &$fragment = null, ?string &$error = null): bool
    {
        if ($baseUrl === null && $url === '') {
            $error = 'Url "" is empty.';

            return false;
        }

        // Split the url in its main components.
        self::split($url, $schemeString, $authorityString, $pathString);

        // Parse scheme.
        if (!self::parseScheme($baseUrl, $schemeString, $scheme, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // Parse authority.
        if (!self::parseAuthority($baseUrl, $authorityString, $host, $port, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // Set default port if needed.
        if ($port === null) {
            $port = $scheme->getDefaultPort();
        }

        // Parse path.
        if (!self::parsePath($baseUrl, $pathString, $path, $queryString, $fragment, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        return true;
    }

    /**
     * Splits a url in its main components.
     *
     * @param string      $url             The url.
     * @param string|null $schemeString    The scheme or null if scheme is not present.
     * @param string|null $authorityString The authority part or null if authority part is not present.
     * @param string|null $pathString      The path or null if path is not present.
     */
    private static function split(string $url, ?string &$schemeString = null, ?string &$authorityString = null, ?string &$pathString = null): void
    {
        $schemeString = null;
        $authorityString = null;
        $pathString = null;

        $parts = explode('://', $url, 2);

        if (count($parts) === 2) {
            // Absolute url.
            $schemeString = $parts[0];
            $parts = explode('/', $parts[1], 2);
            $authorityString = $parts[0];
            $pathString = '/' . (count($parts) === 2 ? $parts[1] : '');

            return;
        }

        if (substr($url, 0, 2) === '//') {
            // Relative url beginning with "//".
            $parts = explode('/', substr($url, 2), 2);
            $authorityString = $parts[0];
            $pathString = '/' . (count($parts) === 2 ? $parts[1] : '');

            return;
        }

        // Relative url as a path.
        $pathString = $url;
    }

    /**
     * Parse scheme.
     *
     * @param UrlInterface|null    $baseUrl      The base url or null if no base url is present.
     * @param string|null          $schemeString The scheme that is to be parsed or null if no scheme is present.
     * @param SchemeInterface|null $scheme       The scheme if parsing was successful, undefined otherwise.
     * @param string|null          $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseScheme(?UrlInterface $baseUrl, ?string $schemeString, ?SchemeInterface &$scheme = null, ?string &$error = null): bool
    {
        if ($schemeString === null) {
            if ($baseUrl === null) {
                $error = 'Scheme is missing.';

                return false;
            }

            $scheme = $baseUrl->getScheme();

            return true;
        }

        try {
            $scheme = Scheme::parse($schemeString);
        } catch (SchemeInvalidArgumentException $e) {
            $error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Parse authority part.
     *
     * @param UrlInterface|null  $baseUrl         The base url or null if no base url is present.
     * @param string|null        $authorityString The authority part that is to be parsed or null if no authority part is present.
     * @param HostInterface|null $host            The host if parsing was successful, undefined otherwise.
     * @param int|null           $port            The port if parsing was successful, undefined otherwise.
     * @param string|null        $error           The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseAuthority(?UrlInterface $baseUrl, ?string $authorityString, ?HostInterface &$host = null, ?int &$port = null, ?string &$error = null): bool
    {
        if ($authorityString === null && $baseUrl !== null) {
            $host = $baseUrl->getHost();
            $port = $baseUrl->getPort();

            return true;
        }

        // Throw away username and password if present.
        $parts = explode('@', $authorityString, 2);
        if (count($parts) > 1) {
            $authorityString = $parts[1];
        }

        $parts = explode(':', $authorityString, 2);
        $port = null;

        // Try parse and validate port.
        if (count($parts) === 2 && $parts[1] !== '') {
            // Port containing invalid character is invalid.
            if (preg_match('/[^0-9]/', $parts[1], $matches)) {
                $error = 'Port "' . $parts[1] . '" contains invalid character "' . $matches[0] . '".';

                return false;
            }

            $port = intval($parts[1]);

            // Port out of range is invalid.
            if (!self::validatePort($port, $error)) {
                return false;
            }
        }

        try {
            $host = Host::parse($parts[0]);
        } catch (HostInvalidArgumentException $e) {
            $error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Parse path.
     *
     * @param UrlInterface|null     $baseUrl     The base url or null if no base url is present.
     * @param string                $pathString  The path that is to be parsed.
     * @param UrlPathInterface|null $path        The path if parsing was successful, undefined otherwise.
     * @param string|null           $queryString The query string if parsing was successful, undefined otherwise.
     * @param string|null           $fragment    The fragment if parsing was successful, undefined otherwise.
     * @param string|null           $error       The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parsePath(?UrlInterface $baseUrl, string $pathString, ?UrlPathInterface &$path = null, ?string &$queryString = null, ?string &$fragment = null, ?string &$error = null): bool
    {
        // Fragment.
        $parts = explode('#', $pathString, 2);
        $pathString = $parts[0];
        $fragment = count($parts) > 1 ? $parts[1] : null;

        if (!self::validateFragment($fragment, $error)) {
            return false;
        }

        // Query string.
        $parts = explode('?', $pathString, 2);
        $pathString = $parts[0];
        $queryString = count($parts) > 1 ? $parts[1] : null;

        if (!self::validateQueryString($queryString, $error)) {
            return false;
        }

        // Try parse url path.
        if (!self::parseUrlPath($baseUrl, $pathString, $path, $error)) {
            return false;
        }

        // If path is empty and there is a base url, handle query string and fragment.
        if ($pathString === '' && $baseUrl !== null && $queryString === null) {
            $queryString = $baseUrl->getQueryString();
            $fragment = $fragment ?: $baseUrl->getFragment();
        }

        return true;
    }

    /**
     * Try to validate or parse path.
     *
     * @param UrlInterface|null     $baseUrl    The base url or null if no base url is present.
     * @param string                $pathString The path that is to be parsed.
     * @param UrlPathInterface|null $path       The path if parsing was successful, undefined otherwise.
     * @param string|null           $error      The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function parseUrlPath(?UrlInterface $baseUrl, string $pathString, ?UrlPathInterface &$path = null, ?string &$error = null): bool
    {
        // If path is empty and there is a base url, use the path from base url.
        if ($baseUrl !== null && $pathString === '') {
            $path = $baseUrl->getPath();

            return true;
        }

        // Parse path.
        try {
            $path = UrlPath::parse($pathString);
        } catch (UrlPathInvalidArgumentException $e) {
            $error = $e->getMessage();

            return false;
        }

        // If there is a base url, paths should be combined.
        if ($baseUrl !== null) {
            $path = $baseUrl->getPath()->withUrlPath($path);
        }

        return true;
    }

    /**
     * Validates parts of url.
     *
     * @param int              $port        The port.
     * @param UrlPathInterface $urlPath     The url path.
     * @param string|null      $queryString The query string or null if no query string should be used.
     * @param string|null      $fragment    The fragment or null if no fragment should be used.
     * @param string|null      $error       The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateParts(int $port, UrlPathInterface $urlPath, ?string $queryString, ?string $fragment, ?string &$error): bool
    {
        // Validate port.
        if (!self::validatePort($port, $error)) {
            return false;
        }

        // Url path can not be relative.
        if ($urlPath->isRelative()) {
            $error = 'Url path "' . $urlPath . '" is relative.';

            return false;
        }

        // Validate query string.
        if (!self::validateQueryString($queryString, $error)) {
            return false;
        }

        // Validate fragment.
        if (!self::validateFragment($fragment, $error)) {
            return false;
        }

        return true;
    }

    /**
     * Validates a port.
     *
     * @param int    $port  The port.
     * @param string $error The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validatePort(int $port, ?string &$error): bool
    {
        // Port below 0 is invalid.
        if ($port < 0) {
            $error = 'Port ' . $port . ' is out of range: Minimum port number is 0.';

            return false;
        }

        // Port above 65535 is invalid.
        if ($port > 65535) {
            $error = 'Port ' . $port . ' is out of range: Maximum port number is 65535.';

            return false;
        }

        return true;
    }

    /**
     * Validates a query string.
     *
     * @param string|null $queryString The query string.
     * @param string      $error       The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateQueryString(?string $queryString, ?string &$error): bool
    {
        if ($queryString === null) {
            return true;
        }

        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*\+,;=:@\[\]\/\?%-]/', $queryString, $matches)) {
            $error = 'Query string "' . $queryString . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * Validates a fragment.
     *
     * @param string|null $fragment The fragment.
     * @param string      $error    The error text if validation was not successful, undefined otherwise.
     *
     * @return bool True if validation was successful, false otherwise.
     */
    private static function validateFragment(?string $fragment, ?string &$error): bool
    {
        if ($fragment === null) {
            return true;
        }

        if (preg_match('/[^0-9a-zA-Z._~!\$&\'()*\+,;=:@\[\]\/\?%-]/', $fragment, $matches)) {
            $error = 'Fragment "' . $fragment . '" contains invalid character "' . $matches[0] . '".';

            return false;
        }

        return true;
    }

    /**
     * @var SchemeInterface My scheme.
     */
    private $scheme;

    /**
     * @var HostInterface My host.
     */
    private $host;

    /**
     * @var int My port.
     */
    private $port;

    /**
     * @var UrlPathInterface My path.
     */
    private $path;

    /**
     * @var string|null My query string.
     */
    private $queryString;

    /**
     * @var string|null My fragment.
     */
    private $fragment;
}
