<?php

namespace DataTypes;

use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\Interfaces\SchemeInterface;
use DataTypes\Interfaces\UrlInterface;

/**
 * Class representing a Url.
 */
class Url implements UrlInterface
{
    /**
     * @return Interfaces\SchemeInterface The scheme of the url.
     */
    public function getScheme()
    {
        return $this->myScheme;
    }

    /**
     * Returns a copy of the Url instance with the specified scheme.
     *
     * @param SchemeInterface $scheme The scheme.
     *
     * @return UrlInterface The Url instance.
     */
    public function withScheme(SchemeInterface $scheme)
    {
        return new self($scheme, $this->myRest);
    }

    /**
     * @return string The Url as a string.
     */
    public function __toString()
    {
        return $this->myScheme . '://' . $this->myRest;
    }

    /**
     * Checks if a url is valid.
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
     * @param string $url The url.
     *
     * @throws UrlInvalidArgumentException If the $url parameter is not a valid url.
     *
     * @return UrlInterface The Url instance.
     */
    public static function parse($url)
    {
        assert(is_string($url), '$url is not a string');

        if (!static::myParse($url, false, $scheme, $theRest, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($scheme, $theRest);
    }

    /**
     * Parses a url.
     *
     * @param string $url The url.
     *
     * @return UrlInterface|null The Url instance if the $url parameter is a valid url, null otherwise.
     */
    public static function tryParse($url)
    {
        assert(is_string($url), '$url is not a string');

        if (!static::myParse($url, false, $scheme, $theRest)) {
            return null;
        }

        return new self($scheme, $theRest);
    }

    /**
     * Constructs a Url.
     *
     * @param SchemeInterface $scheme  The scheme.
     * @param string          $theRest Temporary variable to use when creating this class.
     */
    private function __construct(SchemeInterface $scheme, $theRest)
    {
        $this->myScheme = $scheme;
        $this->myRest = $theRest;
    }

    /**
     * Tries to parse a Url and returns the result or error text.
     *
     * @param string      $url          The Url.
     * @param bool        $validateOnly If true only validation is performed, if false parse results are returned.
     * @param Scheme      $scheme       The scheme if parsing was successful, undefined otherwise.
     * @param string      $theRest      Temporary variable to use when creating this class.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParse($url, $validateOnly, Scheme &$scheme = null, &$theRest = null, &$error = null)
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

        // fixme: User
        // fixme: Password
        // fixme: Host
        // fixme: Port
        // fixme: Path
        // fixme: Query
        // fixme: Fragment
        // fixme: Relative vs. Absolute

        // fixme: Remove this
        $theRest = $parsedUrl;

        return true;
    }

    /**
     * @param string      $parsedUrl    The part of url that is to be parsed.
     * @param bool        $validateOnly If true only validation is performed, if false parse results are returned.
     * @param Scheme|null $scheme       The scheme if parsing was successful, undefined otherwise.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function myParseScheme(&$parsedUrl, $validateOnly, Scheme &$scheme = null, &$error = null)
    {
        $parts = explode('://', $parsedUrl, 2);
        if (count($parts) < 2) {
            $error = 'Scheme is missing.';

            return false;
        }

        // Try parse scheme.
        if (!$validateOnly) {
            try {
                $scheme = Scheme::parse($parts[0]);
            } catch (SchemeInvalidArgumentException $e) {
                $error = $e->getMessage();

                return false;
            }
        } else {
            return Scheme::isValid($parts[0]);
        }

        $parsedUrl = $parts[1];

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
     * @var string Temporary variable to use when creating this class.
     */
    private $myRest;

    /**
     * @var Scheme My scheme.
     */
    private $myScheme;
}
