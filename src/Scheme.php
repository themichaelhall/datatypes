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
     * Constructs a scheme.
     *
     * @param string $scheme The scheme.
     */
    public function __construct($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        if (!static::_parse($scheme, false, $result, $type, $error)) {
            throw new SchemeInvalidArgumentException($error);
        }

        $this->_build($result, $type);
    }

    /**
     * @return int The type of the scheme.
     */
    public function getType()
    {
        return $this->_type;
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
     * Parses a scheme and returns a Scheme instance.
     *
     * @param string $scheme The scheme.
     *
     * @return SchemeInterface|null The Scheme instance if the $scheme parameter is a valid scheme, null otherwise.
     */
    public static function tryParse($scheme)
    {
        assert(is_string($scheme), '$scheme is not a string');

        try {
            $result = new self($scheme);

            return $result;
        } catch (SchemeInvalidArgumentException $e) {
        }

        return null;
    }

    /**
     * Builds this scheme from scheme parts.
     *
     * @param string $scheme The scheme.
     * @param int    $type   The type.
     */
    private function _build($scheme, $type)
    {
        $this->_scheme = $scheme;
        $this->_type = $type;
    }

    /**
     * Tries to parse a scheme and returns the result or error text.
     *
     * @param string      $scheme       The scheme.
     * @param bool        $validateOnly If true only validation is performed, if false parse results are returned.
     * @param string|null $result       The result if parsing was successful, undefined otherwise.
     * @param int|null    $type         The type if parsing was successful, undefined otherwise.
     * @param string|null $error        The error text if parsing was not successful, undefined otherwise.
     *
     * @return bool True if parsing was successful, false otherwise.
     */
    private static function _parse($scheme, $validateOnly, &$result = null, &$type = null, &$error = null)
    {
        // Pre-validate scheme.
        if (!static::_preValidate($scheme, $error)) {
            return false;
        }

        $result = $scheme;

        // Not existing scheme is invalid.
        if (!isset(static::$_schemes[$result])) {
            $error = 'Scheme "' . $scheme . '" is invalid: Scheme must be "http" or "https"';

            return false;
        }

        // Save the result.
        if (!$validateOnly) {
            $schemeInfo = static::$_schemes[$result];
            $type = $schemeInfo[0];
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
        'http'  => [self::TYPE_HTTP],
        'https' => [self::TYPE_HTTPS],
    ];
}
