<?php
/**
 * This file is a part of the datatypes package.
 *
 * Read more at https://phpdatatypes.com/
 */

namespace DataTypes;

use DataTypes\Exceptions\HostInvalidArgumentException;
use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\Exceptions\UrlPathInvalidArgumentException;
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
     * Returns the host of the url.
     *
     * @since 1.0.0
     *
     * @return HostInterface The host of the url.
     */
    public function getHost()
    {
        return $this->myHost;
    }

    /**
     * Returns the path of the url.
     *
     * @since 1.0.0
     *
     * @return UrlPathInterface The path of the url.
     */
    public function getPath()
    {
        return $this->myPath;
    }

    /**
     * Returns the port of the url.
     *
     * @since 1.0.0
     *
     * @return int The port of the url.
     */
    public function getPort()
    {
        return $this->myPort;
    }

    /**
     * Returns the query string of the url or null if the url has no query string.
     *
     * @since 1.0.0
     *
     * @return string|null The query string of the url or null if the url has no query string.
     */
    public function getQueryString()
    {
        return $this->myQueryString;
    }

    /**
     * Returns the scheme of the url.
     *
     * @since 1.0.0
     *
     * @return SchemeInterface The scheme of the url.
     */
    public function getScheme()
    {
        return $this->myScheme;
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
    public function withHost(HostInterface $host)
    {
        return new self($this->myScheme, $host, $this->myPort, $this->myPath);
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
    public function withScheme(SchemeInterface $scheme, $keepDefaultPort = true)
    {
        return new self($scheme, $this->myHost, ($keepDefaultPort && $this->myPort === $this->myScheme->getDefaultPort() ? $scheme->getDefaultPort() : $this->myPort), $this->myPath);
    }

    /**
     * Returns the url as a string.
     *
     * @since 1.0.0
     *
     * @return string The url as a string.
     */
    public function __toString()
    {
        return $this->myScheme . '://' . $this->myHost . ($this->myPort !== $this->myScheme->getDefaultPort() ? (':' . $this->myPort) : '') . $this->myPath . ($this->myQueryString !== null ? '?' . $this->myQueryString : '');
    }

    /**
     * Creates a url from url parts.
     *
     * @since 1.0.0
     *
     * @param SchemeInterface  $scheme      The scheme.
     * @param HostInterface    $host        The host.
     * @param int|null         $port        The port or null if default port for the scheme should be used.
     * @param UrlPathInterface $urlPath     The url path.
     * @param null             $queryString The query string or null if no query string should be used.
     *
     * @throws \InvalidArgumentException   If any of the parameters are of invalid type.
     * @throws UrlInvalidArgumentException If any of the parameters are invalid.
     *
     * @return UrlInterface The url.
     */
    public static function fromParts(SchemeInterface $scheme, HostInterface $host, $port, UrlPathInterface $urlPath, $queryString = null)
    {
        if (!is_int($port) && !is_null($port)) {
            throw new \InvalidArgumentException('$port parameter is not an integer or null.');
        }

        if (!is_string($queryString) && !is_null($queryString)) {
            throw new \InvalidArgumentException('$queryString parameter is not a string or null.');
        }

        if ($port === null) {
            $port = $scheme->getDefaultPort();
        }

        // Validate port.
        if (!self::myValidatePort($port, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        // Url path can not be relative.
        if ($urlPath->isRelative()) {
            throw new UrlInvalidArgumentException('Url path "' . $urlPath . '" is relative.');
        }

        // fixme: Validate $queryString

        return new self($scheme, $host, $port, $urlPath, $queryString);
    }

    /**
     * Checks if a url is valid.
     *
     * @since 1.0.0
     *
     * @param string $url The url.
     *
     * @throws \InvalidArgumentException If the $url parameter is not a string.
     *
     * @return bool True if the $url parameter is a valid url, false otherwise.
     */
    public static function isValid($url)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('$url parameter is not a string.');
        }

        return self::myParse(null, $url, true);
    }

    /**
     * Parses a url.
     *
     * @since 1.0.0
     *
     * @param string $url The url.
     *
     * @throws \InvalidArgumentException   If the $url parameter is not a string.
     * @throws UrlInvalidArgumentException If the $url parameter is not a valid url.
     *
     * @return UrlInterface The Url instance.
     */
    public static function parse($url)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('$url parameter is not a string.');
        }

        if (!self::myParse(null, $url, false, $scheme, $host, $port, $path, $queryString, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($scheme, $host, $port, $path, $queryString);
    }

    /**
     * Parses a relative url and combines it with a base url.
     *
     * @since 1.0.0
     *
     * @param string       $url     The url
     * @param UrlInterface $baseUrl The base url.
     *
     * @throws \InvalidArgumentException   If the $url parameter is not a string.
     * @throws UrlInvalidArgumentException If the $url parameter is not a valid relative url.
     *
     * @return UrlInterface The Url instance.
     */
    public static function parseRelative($url, UrlInterface $baseUrl)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('$url parameter is not a string.');
        }

        if (!self::myParse($baseUrl, $url, false, $scheme, $host, $port, $path, $queryString, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($scheme, $host, $port, $path, $queryString);
    }

    /**
     * Parses a url.
     *
     * @since 1.0.0
     *
     * @param string $url The url.
     *
     * @throws \InvalidArgumentException If the $url parameter is not a string.
     *
     * @return UrlInterface|null The Url instance if the $url parameter is a valid url, null otherwise.
     */
    public static function tryParse($url)
    {
        if (!is_string($url)) {
            throw new \InvalidArgumentException('$url parameter is not a string.');
        }

        if (!self::myParse(null, $url, false, $scheme, $host, $port, $path, $queryString)) {
            return null;
        }

        return new self($scheme, $host, $port, $path, $queryString);
    }

    /**
     * Constructs a Url.
     *
     * @param SchemeInterface  $scheme      The scheme.
     * @param HostInterface    $host        The host.
     * @param int              $port        The port.
     * @param UrlPathInterface $path        The path.
     * @param string|null      $queryString The query string.
     */
    private function __construct(SchemeInterface $scheme, HostInterface $host, $port, UrlPathInterface $path, $queryString = null)
    {
        $this->myScheme = $scheme;
        $this->myHost = $host;
        $this->myPort = $port;
        $this->myPath = $path;
        $this->myQueryString = $queryString;
    }

    /**
     * Tries to parse a url and returns the result or error text.
     *
     * @param UrlInterface          $baseUrl      The base url or null if no base url is present.
     * @param string                $url          The url.
     * @param bool                  $validateOnly If true only validation is performed, if false parse results are returned.
     * @param SchemeInterface|null  $scheme       The scheme if parsing was successful, undefined otherwise.
     * @param HostInterface|null    $host         The host if parsing was successful, undefined otherwise.
     * @param int|null              $port         The port if parsing was successful, undefined otherwise.
     * @param UrlPathInterface|null $path         The path if parsing was successful, undefined otherwise.
     * @param string|null           $queryString  The query string if parsing was successful, undefined otherwise.
     * @param string|null           $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse(UrlInterface $baseUrl = null, $url, $validateOnly, SchemeInterface &$scheme = null, HostInterface &$host = null, &$port = null, UrlPathInterface &$path = null, &$queryString = null, &$error = null)
    {
        if ($url === '') {
            $error = 'Url "" is empty.';

            return false;
        }

        $schemeString = null;
        $authorityString = null;
        $pathString = null;

        // Split the url in scheme, authority and path parts.
        $parts = explode('://', $url, 2);
        if (count($parts) === 2) {
            $schemeString = $parts[0];
            $parts = explode('/', $parts[1], 2);
            $authorityString = $parts[0];
            $pathString = '/' . (count($parts) === 2 ? $parts[1] : '');
        } elseif (substr($url, 0, 2) === '//') {
            $parts = explode('/', substr($url, 2), 2);
            $authorityString = $parts[0];
            $pathString = '/' . (count($parts) === 2 ? $parts[1] : '');
        } else {
            $pathString = $url;
        }

        // Parse scheme.
        if (!self::myParseScheme($baseUrl, $schemeString, $validateOnly, $scheme, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // Parse authority.
        // fixme: User
        // fixme: Password
        if (!self::myParseAuthority($baseUrl, $authorityString, $validateOnly, $host, $port, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // Set default port if needed.
        if ($port === null && !$validateOnly) {
            $port = $scheme->getDefaultPort();
        }

        // Parse path.
        // fixme: Fragment
        if (!self::myParsePath($baseUrl, $pathString, $validateOnly, $path, $queryString, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        return true;
    }

    /**
     * Parse scheme.
     *
     * @param UrlInterface         $baseUrl      The base url or null if no base url is present.
     * @param string               $schemeString The scheme that is to be parsed.
     * @param bool                 $validateOnly If true only validation is performed, if false parse results are returned.
     * @param SchemeInterface|null $scheme       The scheme if parsing was successful, undefined otherwise.
     * @param string|null          $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParseScheme(UrlInterface $baseUrl = null, $schemeString = null, $validateOnly, SchemeInterface &$scheme = null, &$error = null)
    {
        if ($schemeString === null) {
            if ($baseUrl === null) {
                $error = 'Scheme is missing.';

                return false;
            }

            $scheme = $baseUrl->getScheme();

            return true;
        }

        // Validate or try parse scheme.
        if ($validateOnly) {
            return Scheme::isValid($schemeString);
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
     * @param UrlInterface       $baseUrl         The base url or null if no base url is present.
     * @param string             $authorityString The authority part that is to be parsed.
     * @param bool               $validateOnly    If true only validation is performed, if false parse results are returned.
     * @param HostInterface|null $host            The host if parsing was successful, undefined otherwise.
     * @param int|null           $port            The port if parsing was successful, undefined otherwise.
     * @param string|null        $error           The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParseAuthority(UrlInterface $baseUrl = null, $authorityString, $validateOnly, HostInterface &$host = null, &$port = null, &$error = null)
    {
        if ($authorityString === null && $baseUrl !== null) {
            $host = $baseUrl->getHost();
            $port = $baseUrl->getPort();

            return true;
        }

        $parts = explode(':', $authorityString, 2);
        $port = null;

        // Try parse and validate port.
        if (count($parts) === 2) {
            // Port containing invalid character is invalid.
            if (preg_match('/[^0-9]/', $parts[1], $matches)) {
                $error = 'Port "' . $parts[1] . '" contains invalid character "' . $matches[0] . '".';

                return false;
            }

            $port = intval($parts[1]);

            // Port out of range is invalid.
            if (!self::myValidatePort($port, $error)) {
                return false;
            }
        }

        // Validate or try parse host.
        if ($validateOnly) {
            return Host::isValid($parts[0]);
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
     * @param UrlInterface          $baseUrl      The base url or null if no base url is present.
     * @param string                $pathString   The path that is to be parsed.
     * @param bool                  $validateOnly If true only validation is performed, if false parse results are returned.
     * @param UrlPathInterface|null $path         The path if parsing was successful, undefined otherwise.
     * @param string|null           $queryString  The query string if parsing was successful, undefined otherwise.
     * @param string|null           $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParsePath(UrlInterface $baseUrl = null, $pathString, $validateOnly, UrlPathInterface &$path = null, &$queryString = null, &$error = null)
    {
        $parts = explode('?', $pathString, 2);
        $queryString = count($parts) === 2 ? $parts[1] : null;

        // Validate or try parse path.
        if ($validateOnly) {
            return UrlPath::isValid($parts[0]);
        }

        try {
            $path = UrlPath::parse($parts[0]);
        } catch (UrlPathInvalidArgumentException $e) {
            $error = $e->getMessage();

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
    private static function myValidatePort($port, &$error)
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
     * @var SchemeInterface My scheme.
     */
    private $myScheme;

    /**
     * @var HostInterface My host.
     */
    private $myHost;

    /**
     * @var int My port.
     */
    private $myPort;

    /**
     * @var UrlPathInterface My path.
     */
    private $myPath;

    /**
     * @var string|null My query string.
     */
    private $myQueryString;
}
