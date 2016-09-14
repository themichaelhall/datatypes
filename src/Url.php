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
     * @return Url The Url instance.
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
     * @return Url The Url instance.
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
     * @throws UrlInvalidArgumentException If any of the parameters are invalid.
     *
     * @return Url The url.
     */
    public static function fromParts(SchemeInterface $scheme, HostInterface $host, $port, UrlPathInterface $urlPath, $queryString = null)
    {
        assert(is_int($port) || is_null($port), '$port is not an int or null');
        assert(is_string($queryString) || is_null($queryString), '$queryString is not a string or null');

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
     * @return bool True if the $url parameter is a valid url, false otherwise.
     */
    public static function isValid($url)
    {
        assert(is_string($url), '$url is not a string');

        return static::myParse($url, true);
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
    public static function parse($url)
    {
        assert(is_string($url), '$url is not a string');

        if (!static::myParse($url, false, $scheme, $host, $port, $path, $queryString, $error)) {
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
     * @return UrlInterface|null The Url instance if the $url parameter is a valid url, null otherwise.
     */
    public static function tryParse($url)
    {
        assert(is_string($url), '$url is not a string');

        if (!static::myParse($url, false, $scheme, $host, $port, $path, $queryString)) {
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
     * Tries to parse a Url and returns the result or error text.
     *
     * @param string                $url          The Url.
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
    private static function myParse($url, $validateOnly, SchemeInterface &$scheme = null, HostInterface &$host = null, &$port = null, UrlPathInterface &$path = null, &$queryString = null, &$error = null)
    {
        // Pre-validate Url.
        if (!static::myPreValidate($url, $error)) {
            return false;
        }

        $parsedUrl = $url;

        // Parse scheme.
        if (!static::myParseScheme($parsedUrl, $validateOnly, $scheme, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // Parse host and port.
        if (!static::myParseHostAndPort($parsedUrl, $validateOnly, $host, $port, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // Set default port if none is given.
        if (!$validateOnly && $port === null) {
            $port = $scheme->getDefaultPort();
        }

        // fixme: User
        // fixme: Password

        if (!static::myParsePath($parsedUrl, $validateOnly, $path, $queryString, $error)) {
            $error = 'Url "' . $url . '" is invalid: ' . $error;

            return false;
        }

        // fixme: Fragment
        // fixme: Relative vs. Absolute

        return true;
    }

    /**
     * Parse scheme.
     *
     * @param string               $parsedUrl    The part of url that is to be parsed.
     * @param bool                 $validateOnly If true only validation is performed, if false parse results are returned.
     * @param SchemeInterface|null $scheme       The scheme if parsing was successful, undefined otherwise.
     * @param string|null          $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParseScheme(&$parsedUrl, $validateOnly, SchemeInterface &$scheme = null, &$error = null)
    {
        $parts = explode('://', $parsedUrl, 2);

        if (count($parts) < 2) {
            $error = 'Scheme is missing.';

            return false;
        }

        $parsedUrl = $parts[1];

        // Validate or try parse scheme.
        if ($validateOnly) {
            return Scheme::isValid($parts[0]);
        }

        try {
            $scheme = Scheme::parse($parts[0]);
        } catch (SchemeInvalidArgumentException $e) {
            $error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Parse host and port.
     *
     * @param string             $parsedUrl    The part of url that is to be parsed.
     * @param bool               $validateOnly If true only validation is performed, if false parse results are returned.
     * @param HostInterface|null $host         The host if parsing was successful, undefined otherwise.
     * @param int|null           $port         The port if parsing was successful, undefined otherwise.
     * @param string|null        $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParseHostAndPort(&$parsedUrl, $validateOnly, HostInterface &$host = null, &$port = null, &$error = null)
    {
        $parts = explode('/', $parsedUrl, 2);
        $parsedUrl = count($parts) > 1 ? $parts[1] : null;

        $hostAndPort = explode(':', $parts[0], 2);
        $port = null;

        // Try parse and validate port.
        if (count($hostAndPort) > 1) {
            // Port containing invalid character is invalid.
            if (preg_match('/[^0-9]/', $hostAndPort[1], $matches)) {
                $error = 'Port "' . $hostAndPort[1] . '" contains invalid character "' . $matches[0] . '".';

                return false;
            }

            $port = intval($hostAndPort[1]);

            // Port out of range is invalid.
            if (!self::myValidatePort($port, $error)) {
                return false;
            }
        }

        // Validate or try parse host.
        if ($validateOnly) {
            return Host::isValid($hostAndPort[0]);
        }

        try {
            $host = Host::parse($hostAndPort[0]);
        } catch (HostInvalidArgumentException $e) {
            $error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Parse path.
     *
     * @param string                $parsedUrl    The part of url that is to be parsed.
     * @param bool                  $validateOnly If true only validation is performed, if false parse results are returned.
     * @param UrlPathInterface|null $path         The path if parsing was successful, undefined otherwise.
     * @param string|null           $queryString  The query string if parsing was successful, undefined otherwise.
     * @param string|null           $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParsePath(&$parsedUrl, $validateOnly, UrlPathInterface &$path = null, &$queryString = null, &$error = null)
    {
        $queryString = null;

        // Get the query string if it exists.
        $parts = explode('?', $parsedUrl, 2);
        if (count($parts) > 1) {
            $queryString = $parts[1];
        }
        // fixme: Validate query string

        // Validate or try parse path.
        if ($validateOnly) {
            return UrlPath::isValid('/' . $parts[0]);
        }

        try {
            $path = UrlPath::parse('/' . $parts[0]);
        } catch (UrlPathInvalidArgumentException $e) {
            $error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Pre-validates a Url.
     *
     * @param string $url   The Url.
     * @param string $error The error text if pre-validation was not successful, undefined otherwise.
     *
     * @return bool True if pre-validation was successful, false otherwise.
     */
    private static function myPreValidate($url, &$error)
    {
        // Empty Url is invalid.
        if ($url === '') {
            $error = 'Url "' . $url . '" is empty.';

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
