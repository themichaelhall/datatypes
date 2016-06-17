<?php

namespace DataTypes;

use DataTypes\Exceptions\UrlInvalidArgumentException;
use DataTypes\Interfaces\UrlInterface;

/**
 * Class representing a Url.
 */
class Url implements UrlInterface
{
    /**
     * @return string The Url as a string.
     */
    public function __toString()
    {
        return $this->_value;
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

        return static::_parse($url);
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

        if (!static::_parse($url, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        return new self($url);
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

        if (!static::_parse($url)) {
            return null;
        }

        return new self($url);
    }

    /**
     * Constructs a Url.
     *
     * @param string $url The Url.
     */
    private function __construct($url)
    {
        $this->_value = $url;
    }

    /**
     * Tries to parse a Url and returns the result or error text.
     *
     * @param string      $url   The Url.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($url, &$error = null)
    {
        // Pre-validate Url.
        if (!static::_preValidate($url, $error)) {
            return false;
        }

        // fixme: Scheme
        // fixme: User
        // fixme: Password
        // fixme: Host
        // fixme: Port
        // fixme: Path
        // fixme: Query
        // fixme: Fragment
        // fixme: Relative vs. Absolute

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
    private static function _preValidate($url, &$error)
    {
        // Empty Url is invalid.
        if ($url === '') {
            $error = 'Url "' . $url . '" is empty.';

            return false;
        }

        return true;
    }

    /**
     * @var string My value.
     */
    private $_value;
}
