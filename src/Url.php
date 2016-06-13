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
     * Constructs a Url.
     *
     * @param string $url The Url.
     */
    public function __construct($url)
    {
        assert(is_string($url), '$url is not a string');

        if (!static::_parse($url, $error)) {
            throw new UrlInvalidArgumentException($error);
        }

        // fixme: Scheme
        // fixme: User
        // fixme: Password
        // fixme: Host
        // fixme: Port
        // fixme: Path
        // fixme: Query
        // fixme: Fragment

        $this->_value = $url;
    }

    /**
     * @return string The Url as a string.
     */
    public function __toString()
    {
        return $this->_value;
    }

    /**
     * Tries to parse a Url and returns the result or error text.
     *
     * @param string      $url   The Url.
     * @param string|null $error The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($url, &$error)
    {
        // Pre-validate Url.
        if (!static::_preValidate($url, $error)) {
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
