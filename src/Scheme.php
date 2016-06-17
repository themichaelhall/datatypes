<?php

namespace DataTypes;

use DataTypes\Exceptions\SchemeInvalidArgumentException;
use DataTypes\Interfaces\SchemeInterface;

/**
 * Class representing a scheme.
 */
class Scheme implements SchemeInterface
{
    /**
     * Scheme type http.
     */
    const TYPE_HTTP = 1;

    /**
     * Scheme type https.
     */
    const TYPE_HTTPS = 2;

    /**
     * @return int The default port of the scheme.
     */
    public function getDefaultPort()
    {
        return $this->_defaultPort;
    }

    /**
     * @return int The type of the scheme.
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return bool True if the scheme is http, false otherwise.
     */
    public function isHttp()
    {
        return $this->_type === self::TYPE_HTTP;
    }

    /**
     * @return bool True if the scheme is https, false otherwise.
     */
    public function isHttps()
    {
        return $this->_type === self::TYPE_HTTPS;
    }

    /**
     * @return string The scheme as a string.
     */
    public function __toString()
    {
        return $this->_scheme;
    }

    /**
     * Checks if a scheme is valid.
     *
     * @param string $scheme The scheme.
     *
     * @return bool True if the $scheme parameter is a valid scheme, false otherwise.
     */
    public static function isValid($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        return static::_parse($scheme, true);
    }

    /**
     * Parses a scheme.
     *
     * @param string $scheme The scheme.
     *
     * @return SchemeInterface The Scheme instance.
     *
     * @throws SchemeInvalidArgumentException If the $scheme parameter is not a valid scheme.
     */
    public static function parse($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        if (!static::_parse($scheme, false, $result, $type, $defaultPort, $error)) {
            throw new SchemeInvalidArgumentException($error);
        }

        return new self($result, $type, $defaultPort);
    }

    /**
     * Parses a scheme.
     *
     * @param string $scheme The scheme.
     *
     * @return SchemeInterface|null The Scheme instance if the $scheme parameter is a valid scheme, null otherwise.
     */
    public static function tryParse($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        if (!static::_parse($scheme, false, $result, $type, $defaultPort)) {
            return null;
        }

        return new self($result, $type, $defaultPort);
    }

    /**
     * Constructs a scheme from scheme info.
     *
     * @param string $scheme      The scheme.
     * @param int    $type        The type.
     * @param int    $defaultPort The default port.
     */
    private function __construct($scheme, $type, $defaultPort)
    {
        $this->_scheme = $scheme;
        $this->_type = $type;
        $this->_defaultPort = $defaultPort;
    }

    /**
     * Tries to parse a scheme and returns the result or error text.
     *
     * @param string      $scheme       The scheme.
     * @param bool        $validateOnly If true only validation is performed, if false parse results are returned.
     * @param string|null $result       The result if parsing was successful, undefined otherwise.
     * @param int|null    $type         The type if parsing was successful, undefined otherwise.
     * @param int|null    $defaultPort  The default port if parsing was successful, undefined otherwise.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($scheme, $validateOnly, &$result = null, &$type = null, &$defaultPort = null, &$error = null)
    {
        // Pre-validate scheme.
        if (!static::_preValidate($scheme, $error)) {
            return false;
        }

        $result = strtolower($scheme);

        // Not existing scheme is invalid.
        if (!isset(static::$_schemes[$result])) {
            $error = 'Scheme "' . $scheme . '" is invalid: Scheme must be "http" or "https"';

            return false;
        }

        // Save the result.
        if (!$validateOnly) {
            $schemeInfo = static::$_schemes[$result];
            $type = $schemeInfo[0];
            $defaultPort = $schemeInfo[1];
        }

        return true;
    }

    /**
     * Pre-validates a scheme.
     *
     * @param string      $scheme The scheme.
     * @param string|null $error  The error text if pre-validation was not successful, undefined otherwise.
     *
     * @return bool True if pre-validation was successful, false otherwise.
     */
    private static function _preValidate($scheme, &$error = null)
    {
        // Empty scheme is invalid.
        if ($scheme === '') {
            $error = 'Scheme "' . $scheme . '" is empty.';

            return false;
        }

        return true;
    }

    /**
     * @var int My default port.
     */
    private $_defaultPort;

    /**
     * @var string My scheme.
     */
    private $_scheme;

    /**
     * @var int My type.
     */
    private $_type;

    /**
     * @var array The valid schemes.
     */
    private static $_schemes = [
        'http'  => [self::TYPE_HTTP, 80],
        'https' => [self::TYPE_HTTPS, 443],
    ];
}
